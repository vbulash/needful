<?php

namespace App\Http\Controllers\planning;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AnswerStudentStatus;
use App\Models\Order;
use App\Models\OrderEmployerStatus;
use App\Models\School;
use App\Models\Student;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class EmployerStudentController extends Controller {
	protected function getQuery(): iterable {
		$context = session('context');
		return DB::select(<<<EOS
SELECT
	students.*,
	ans.status
FROM
	answers,
	students,
	answers_students AS ans
WHERE
	ans.answer_id = answers.id
	AND ans.student_id = students.id
	AND ans.status <> :status
	AND answers.id = :answer
EOS,
			[
				'status' => AnswerStudentStatus::NEW ->value,
				'answer' => $context['answer']
			]
		);
	}

	public function getData() {
		$query = $this->getQuery();

		return DataTables::of($query)
			->editColumn('fio', fn($student) => sprintf("%s %s%s",
				$student->lastname, $student->firstname, $student->surname ? ' ' . $student->surname : '')
			)
			->editColumn('birthdate', fn($student) => (new DateTime($student->birthdate))->format('d.m.Y'))
			->addColumn('status', fn($student) => AnswerStudentStatus::getName($student->status))
			->addColumn('action', function ($student) {
				// $showRoute = route('planning.students.show', ['student' => $student->getKey()]);
				$showRoute = route('employers.students.show', ['student' => $student->id]);
				$approveCall = sprintf("clickChangeStatus(%s, %d)",
					$student->id, AnswerStudentStatus::APPROVED->value);
				$rejectCall = sprintf("clickChangeStatus(%s, %d)",
					$student->id, AnswerStudentStatus::REJECTED->value);
				$items = [];

				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'divider'];
				if ($student->status != AnswerStudentStatus::APPROVED->value)
					$items[] = ['type' => 'item', 'click' => $approveCall, 'icon' => 'fas fa-thumbs-up', 'title' => 'Утвердить практиканта'];
				if ($student->status != AnswerStudentStatus::REJECTED->value)
					$items[] = ['type' => 'item', 'click' => $rejectCall, 'icon' => 'fas fa-thumbs-down', 'title' => 'Отказаться от практиканта'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index() {
		$query = $this->getQuery();
		$count = count($query);
		$selected = [];
		foreach ($query as $student) {
			$selected[$student->id] = $student->status;
		}
		// $students = Answer::find($context['answer'])->students()
		// 	->whereHas('answers', fn($query) => $query->where('status', '<>', AnswerStudentStatus::NEW ->value))
		// 	->get();
		// $count = $students->count();

		return view('employers.students.index', compact('count', 'selected'));
	}

	public function show(int $student) {
		$mode = config('global.show');
		$student = Student::findOrFail($student);
		return view('employers.students.show', compact('student', 'mode'));
	}

	public function changeStatus(Request $request) {
		$status = $request->status;
		$student = $request->student;
		$context = session('context');
		$answer = Answer::find($context['answer']);

		if ($student == 0) {
			$ids = $answer->students->pluck('id')->toArray();
			$answer->students()->syncWithPivotValues($ids, ['status' => $status]);
		} else
			$answer->students()->updateExistingPivot($student, ['status' => $status]);
		return true;
	}
}