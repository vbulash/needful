<?php

namespace App\Http\Controllers\planning;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AnswerStudentStatus;
use App\Models\Order;
use App\Models\OrderEmployerStatus;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class EmployerStudentController extends Controller {
	public function getData() {
		$context = session('context');
		$query = Answer::find($context['answer'])->students;

		return DataTables::of($query)
			->editColumn('fio', fn($student) => $student->getTitle())
			->editColumn('birthdate', fn($student) => $student->birthdate->format('d.m.Y'))
			->addColumn('status', fn($student) => AnswerStudentStatus::getName($student->pivot->status))
			->addColumn('action', function ($student) {
				// $showRoute = route('planning.students.show', ['student' => $student->getKey()]);
				$showRoute = route('employers.students.show', ['student' => $student->getKey()]);
				$approveCall = sprintf("clickChangeStatus(%s, %d)",
					$student->getKey(), AnswerStudentStatus::APPROVED->value);
				$rejectCall = sprintf("clickChangeStatus(%s, %d)",
					$student->getKey(), AnswerStudentStatus::REJECTED->value);
				$items = [];

				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'click' => $approveCall, 'icon' => 'fas fa-thumbs-up', 'title' => 'Утвердить практиканта'];
				$items[] = ['type' => 'item', 'click' => $rejectCall, 'icon' => 'fas fa-thumbs-down', 'title' => 'Отказаться от практиканта'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index() {
		$context = session('context');
		$students = Answer::find($context['answer'])->students;
		$count = count($students);

		return view('employers.students.index', compact('count'));
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