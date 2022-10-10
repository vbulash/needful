<?php

namespace App\Http\Controllers;

use App\Events\InviteTraineeTaskEvent;
use App\Events\ToastEvent;
use App\Models\ActiveStatus;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\Student;
use App\Models\TraineeStatus;
use App\Models\User;
use App\Notifications\e2s\EmployerPracticeNotification;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TraineeController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @param int $internship
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse
	{
		$context = session('context');
		$trainees = DB::select(<<<SQL
SELECT
    hs.id AS id,
    students.id AS stud_id,
    students.lastname AS lastname,
    students.firstname AS firstname,
    students.surname AS surname,
    hs.status AS status
FROM
    history_student AS hs, histories, students
WHERE
    hs.history_id = :history AND
    hs.history_id = histories.id AND
    hs.student_id = students.id
SQL,
			['history' => $context['history']]);
		$query = collect($trainees);

		return Datatables::of($query)
			->editColumn('id', fn($trainee) => $trainee->id)
			->editColumn('fio', fn($trainee) => sprintf("%s %s%s",
				$trainee->lastname, $trainee->firstname, $trainee->surname ? ' ' . $trainee->surname : ''))
			->editColumn('status', fn($trainee) => TraineeStatus::getName($trainee->status))
			->addColumn('action', function ($trainee) use ($context) {
				$showRoute = route('trainees.show', ['trainee' => $trainee->stud_id]);

				$actions = <<<EOB
<a href="{$showRoute}" class="btn btn-primary btn-sm float-left me-5"
	data-toggle="tooltip" data-placement="top" title="Просмотр">
	<i class="fas fa-eye"></i>
</a>
EOB;

				$actions .= sprintf(<<<EOB
<button class="btn btn-primary btn-sm float-left"
	data-toggle="tooltip" data-placement="top" title="Пригласить" onclick="clickAsk({$trainee->stud_id})" %s>
	<i class="fas fa-envelope"></i>
</button>
EOB,
					$trainee->status != TraineeStatus::NEW->value ? 'disabled' : '');

				$actions .= sprintf(<<<EOB
<button class="btn btn-primary btn-sm float-left ms-1"
	data-toggle="tooltip" data-placement="top" title="Отменить" onclick="clickCancel({$trainee->stud_id}, {$trainee->id})" %s>
	<i class="fas fa-ban"></i>
</button>
EOB,
					$trainee->status == TraineeStatus::NEW->value ? 'disabled' : '');

				$actions .= sprintf(<<<EOB
<button class="btn btn-primary btn-sm float-left ms-5"
	data-toggle="tooltip" data-placement="top" title="Разрыв связи" onclick="clickUnlink({$trainee->stud_id}, {$trainee->id})" %s>
	<i class="fas fa-unlink"></i>
</button>
EOB,
				$trainee->status != TraineeStatus::NEW->value ? 'disabled' : '');

				return $actions;
			})
			->make(true);
	}

	private function getStudents(History $history): bool|string
	{
		$used = $history->students
			->pluck('id')
			->toArray();
		$all = Student::all()
			->where('status', ActiveStatus::ACTIVE->value)
			->pluck('id')
			->toArray();

		$students = [];
		$diff = Student::all()
			->whereIn('id', array_values(array_diff($all, $used)))
			->sortBy(['lastname', 'firstname', 'surname'])
			->each(function ($student) use (&$students) {
				$students[] = [
					'id' => $student->getKey(),
					'text' => $student->getTitle(),
					'birthdate' => $student->birthdate->format('d.m.Y'),
					'phone' => $student->phone,
				];
			});
		$new = count(DB::select(<<<EOS
SELECT *
FROM history_student
WHERE
    history_id = :history AND
    status = :status
EOS,
			['history' => $history->getKey(), 'status' => HistoryStatus::NEW->value]
		));

		return json_encode([
			'data' => $students,
			'new' => $new,
			'all' => $history->students()->count()
		]);
	}

	public function index(): Factory|View|Application
	{
		$context = session('context');
		$history = History::findOrFail($context['history']);
		$count = $history->students()->count();
		$students = $this->getStudents($history);

		return view('trainees.index', compact('students', 'count'));
	}

	public function link(Request $request): Response|Application|ResponseFactory
	{
		$context = session('context');
		$history = History::findOrFail($context['history']);
		$history->students()->attach($request->trainees);

		$students = $this->getStudents($history);
		return response(content: $students, status: 200);
	}

	public function unlink(Request $request): Response|Application|ResponseFactory
	{
		$context = session('context');
		$history = History::findOrFail($context['history']);
		$trainee = Student::findOrFail($request->trainee);
		$name = $trainee->getTitle();
		$history->students()->detach($request->trainee);

		$students = $this->getStudents($history);

		event(new ToastEvent('success', '', "Привязка практиканта &laquo;$name&raquo; к практике удалена"));

		return response(content: $students, status: 200);
	}

	private function inviteOne(History $history, Student $trainee): void
	{
		$history->students()->updateExistingPivot($trainee, ['status' => TraineeStatus::ASKED->value]);
		$trainee->notify(new EmployerPracticeNotification($trainee, $history, TraineeStatus::ASKED->value));
		event(new ToastEvent('info', '', "Переслано письмо-предложение практики для учащегося: {$trainee->getTitle()}"));
		event(new InviteTraineeTaskEvent($history, $trainee));
	}

	public function invite(Request $request): Response|Application|ResponseFactory
	{
		$all = $request->all;
		$context = session('context');
		$history = History::findOrFail($context['history']);
		if ($request->has('trainee')) {
			$trainee = $history->students()->findOrFail($request->trainee);
			$this->inviteOne($history, $trainee);
		} else foreach ($history->students as $trainee) {
			if ($all || $trainee->pivot->status == TraineeStatus::NEW->value) {
				$this->inviteOne($history, $trainee);
			}
		}
		return response(status: 200);
	}

	public function show(int $trainee): Factory|View|Application
	{
		$mode = config('global.show');
		$student = Student::findOrFail($trainee);
		$users = User::all();

		return view('trainees.show', compact('mode', 'student', 'users'));
	}
}
