<?php

namespace App\Http\Controllers\planning;

use App\Events\orders\NamesFixedTaskEvent;
use App\Events\orders\NamesInvitedTaskEvent;
use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AnswerStatus;
use App\Models\AnswerStudentStatus;
use App\Models\Order;
use App\Models\OrderEmployerStatus;
use App\Models\School;
use App\Models\Student;
use App\Notifications\orders\FixNames;
use App\Notifications\orders\NamesSchool2Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class StudentController extends Controller {
	public function getData() {
		$context = session('context');
		$answer = Answer::find($context['answer']);
		$query = $answer->students;

		return DataTables::of($query)
			->editColumn('fio', fn($student) => $student->getTitle())
			->editColumn('birthdate', fn($student) => $student->birthdate->format('d.m.Y'))
			->addColumn('status', fn($student) => AnswerStudentStatus::getName($student->pivot->status))
			->addColumn('action', function ($student) use ($answer) {
				$showRoute = route('planning.students.show', ['student' => $student->getKey()]);
				$items = [];

				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				if ($answer->status != AnswerStatus::DONE->value)
					$items[] = ['type' => 'item', 'click' => "clickDelete({$student->getKey()})", 'icon' => 'fas fa-ban', 'title' => 'Удаление практиканта'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index() {
		$context = session('context');
		$students = Answer::find($context['answer'])->students;
		$count = count($students);
		$school = Order::find($context['order'])->school;

		$selected = [];
		$students->each(function ($student) use (&$selected) {
			$id = $student->getKey();
			$text = $student->getTitle();
			$selected[$student->getKey()] = (object) [
				'text' => $student->getTitle(),
				'status' => $student->pivot->status
			];
		});

		$enabled = [];
		$query = DB::select(<<<EOS
select
	students.*
from
	students,
	learns,
	schools
where
	learns.student_id = students.id
	and learns.school_id = schools.id
	and learns.finish is null
	and schools.id = :school
EOS,
			['school' => $school->getKey()]);
		foreach ($query as $student) {
			$id = $student->id;
			if (array_key_exists($id, $selected))
				continue;
			$enabled[$id] = (object) [
				'text' => sprintf("%s %s%s",
					$student->lastname, $student->firstname, $student->surname ? ' ' . $student->surname : ''),
				'status' => 0,
			];
		}

		return view('planning.students.index', compact('count', 'selected', 'enabled'));
	}

	public function show(int $student) {
		$mode = config('global.show');
		$student = Student::findOrFail($student);
		return view('planning.students.show', compact('student', 'mode'));
	}

	public function store(Request $request) {
		$context = session('context');
		$answer = Answer::find($context['answer']);
		$student = $request->id;
		$text = $request->text;

		$answer->students()
			->syncWithoutDetaching([$student => ['status' => AnswerStudentStatus::NEW ->value]]);

		return true;
	}

	public function destroy(Request $request) {
		$answer = Answer::find($request->answer);
		$student = Student::find($request->id);

		$answer->students()->detach($student);
		return true;
	}

	public function send(int $answer) {
		$_answer = Answer::find($answer);

		$_answer->update([
			'status' => AnswerStatus::NAMES->value
		]);

		foreach ($_answer->students as $student) {
			if ($student->pivot->status == AnswerStudentStatus::NEW ->value)
				$_answer->students()->updateExistingPivot($student, ['status' => AnswerStudentStatus::INVITED->value]);
		}

		$_answer->employer->user->notify(new NamesSchool2Employer($_answer));
		event(new NamesInvitedTaskEvent($_answer));

		event(new ToastEvent('success', '', 'Вы уведомили работодателя о предложении практикантов'));
		return true;
	}

	public function fix(int $answer) {
		$context = session('context');
		$order = $context['order'];
		$_answer = Answer::find($answer);

		// TODO закрыто на время отладки
		$_answer->update([
			'status' => AnswerStatus::DONE->value
		]);

		$_answer->employer->user->notify(new FixNames($_answer));
		event(new NamesFixedTaskEvent($_answer));

		foreach ($_answer->students as $done_student) {
			$query = DB::select(<<<EOS
SELECT
	a.id AS aid,
	s.id AS sid
FROM
	`students` AS s,
	`answers` AS a,
	`answers_students` AS ans,
	`orders_specialties` AS os
WHERE
	s.id = :student
	AND ans.`answer_id` = a.`id`
	AND ans.`student_id` = s.`id`
	AND a.`id` <> :answer
	AND os.`id` = a.`orders_specialties_id`
	AND os.`order_id` = :order
EOS, [
					'student' => $done_student->getKey(),
					'answer' => $answer,
					'order' => $order
				]);
			foreach ($query as $temp_answer) {
				$_temp_answer = Answer::find($temp_answer->aid);
				$_temp_answer->students()->updateExistingPivot($temp_answer->sid,
					['status' => AnswerStudentStatus::RESERVED->value]);
			}

		}

		event(new ToastEvent('success', '', 'Заявка с практикантами полностью зафиксирована'));
		return redirect()->route('planning.students.index');
	}
}