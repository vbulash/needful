<?php

namespace App\Http\Controllers;

use App\Events\Asked2AcceptedTaskEvent;
use App\Events\InviteTraineeTaskEvent;
use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Models\ActiveStatus;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\Student;
use App\Models\Trainee;
use App\Models\TraineeStatus;
use App\Models\User;
use App\Notifications\e2s\Accepted2ApprovedNotification;
use App\Notifications\e2s\Accepted2CancelledNotification;
use App\Notifications\e2s\All2DestroyedNotification;
use App\Notifications\e2s\Asked2AcceptedNotification;
use App\Notifications\e2s\Asked2ApprovedNotification;
use App\Notifications\e2s\Asked2CancelledNotification;
use App\Notifications\e2s\Asked2RejectedNotification;
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
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse
	{
		$context = session('context');
		$trainees = DB::select(<<<SQL
SELECT
    hs.id AS id,
    histories.id AS hid,
    students.id AS stud_id,
    students.lastname AS lastname,
    students.firstname AS firstname,
    students.surname AS surname,
    students.email,
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
		if (auth()->user()->hasRole(RoleName::TRAINEE->value))
			$query = $query->filter(function($item) {
				$student = Student::findOrFail($item->stud_id);
				$temp = auth()->user()->isAllowed($student);
				return $temp;
			});

		return Datatables::of($query)
			->editColumn('id', fn($trainee) => $trainee->id)
			->editColumn('fio', fn($trainee) => sprintf("%s %s%s",
				$trainee->lastname, $trainee->firstname, $trainee->surname ? ' ' . $trainee->surname : ''))
			->editColumn('status', fn($trainee) => TraineeStatus::getName($trainee->status))
			->addColumn('action', function ($trainee) use ($context) {
				$showRoute = route('trainees.show', ['trainee' => $trainee->stud_id]);
				$actions = '';

				if (auth()->user()->can('histories.list'))
					$actions .= <<<EOB
<a href="{$showRoute}" class="btn btn-primary btn-sm float-left me-5"
	data-toggle="tooltip" data-placement="top" title="Просмотр">
	<i class="fas fa-eye"></i>
</a>
EOB;

				foreach (TraineeStatus::getAdminButtons() as $button) {
					$actions .= sprintf(<<<EOB
<button class="btn btn-primary btn-sm float-left ms-1 transition"
	onclick="clickTransition(this)"
	data-history="%d" data-student="%d" data-from="%s" data-to="%s" data-callback="%s"
	data-toggle="tooltip" data-placement="top" title="%s" %s>
	<i class="%s"></i>
</button>
EOB,
						$trainee->hid, $trainee->stud_id, $trainee->status, $button['to'], $button['callback'],
						$button['title'], TraineeStatus::allowed($trainee->status, $button['to']) ? '' : 'disabled',
						$button['icon']
					);
				};

				if (auth()->user()->can('histories.edit'))
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

	public function show(int $trainee): Factory|View|Application
	{
		$mode = config('global.show');
		$student = Student::findOrFail($trainee);
		$users = User::all();

		return view('trainees.show', compact('mode', 'student', 'users'));
	}

	public function inviteAll(Request $request): Response|Application|ResponseFactory
	{
		$all = $request->all;
		$context = session('context');
		$history = History::findOrFail($context['history']);
		if ($request->has('trainee')) {
			$trainee = $history->students()->findOrFail($request->trainee);
			$this->invite($history, $trainee);
		} else foreach ($history->students as $trainee) {
			if ($all || $trainee->pivot->status == TraineeStatus::NEW->value) {
				$this->invite($history, $trainee);
			}
		}
		return response(status: 200);
	}

	public function transition(Request $request): Response|Application|ResponseFactory
	{
		$history = History::findOrFail($request->history);
		$employer = $history->timetable->internship->employer;
		$student = Student::findOrFail($request->student);
		$from = $request->from;
		$to = $request->to;

		$message = '';
		$history->students()->updateExistingPivot($student, ['status' => $to]);

		if ($to == TraineeStatus::DESTROYED->value) {
			auth()->user()->notify(new All2DestroyedNotification($history, $student));
			$message = "Практика отменена полностью. Индивидуальные приглашения отменены независимо от их текущего статуса";
		} else switch ($from) {
			case TraineeStatus::NEW->value:
				if ($to == TraineeStatus::ASKED->value) {
					$this->invite($history, $student);
					$message = "Переслано предложение практики учащемуся &laquo;{$student->getTitle()}&raquo;";
				}
				break;
			case TraineeStatus::ASKED->value:
				switch ($to) {
					case TraineeStatus::ACCEPTED->value:
						event(new Asked2AcceptedTaskEvent($history, $student));
						$employer->user->notify(new Asked2AcceptedNotification($history, $student));
						$message = "Учащийся &laquo;{$student->getTitle()}&raquo; согласился с предложением практики";
						break;
					case TraineeStatus::REJECTED->value:
						$employer->user->notify(new Asked2RejectedNotification($history, $student));
						$message = "Учащийся &laquo;{$student->getTitle()}&raquo; отказался от предложения практики";
						break;
					case TraineeStatus::APPROVED->value:
						$student->notify(new Asked2ApprovedNotification($history, $student));
						$message = "Учащийся &laquo;{$student->getTitle()}&raquo; утвержден для прохождения практики";
						break;
					case TraineeStatus::CANCELLED->value:
						$student->notify(new Asked2CancelledNotification($history, $student));
						$message = "Предложение практики для учащегося &laquo;{$student->getTitle()}&raquo; отменено";
						break;
				}
				break;
			case TraineeStatus::ACCEPTED->value:
				switch ($to) {
					case TraineeStatus::APPROVED->value:
						$student->notify(new Accepted2ApprovedNotification($history, $student));
						$message = "Учащийся &laquo;{$student->getTitle()}&raquo; утвержден для прохождения практики";
						break;
					case TraineeStatus::CANCELLED->value:
						$student->notify(new Accepted2CancelledNotification($history, $student));
						$message = "Предложение практики для учащегося &laquo;{$student->getTitle()}&raquo; отменено";
						break;
				}
		}
		if ($message)
			event(new ToastEvent('info', '', $message));
		return response(status: 200);
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Обработчики перехода по статусам
	private function invite(History $history, Student $student): void
	{
		// Нотификация и сообщение по итогам смены статуса
		$history->students()->updateExistingPivot($student, ['status' => TraineeStatus::ASKED->value]);
		$student->notify(new EmployerPracticeNotification($student, $history, TraineeStatus::ASKED->value));
		event(new InviteTraineeTaskEvent($history, $student));

		$student->user->allow($history);
		$student->user->allow($history->timetable->internship->employer);

		event(new ToastEvent('info', '', "Переслано предложение практики учащемуся &laquo;{$student->getTitle()}&raquo;"));
	}
}
