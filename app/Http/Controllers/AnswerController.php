<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Models\Answer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AnswerController extends Controller {
	private function getQuery(int $employer, int $order) {
		return DB::select(<<<SQL
SELECT
    a.id,
	s.name,
	os.quantity,
	a.approved
FROM
    answers AS a,
    orders_specialties AS os,
    specialties AS s
WHERE
    a.orders_specialties_id = os.id
        AND os.specialty_id = s.id
		AND os.order_id = :order
		AND a.employer_id = :employer
SQL,
			[
				'order' => $order,
				'employer' => $employer
			]);
	}

	public function getData(int $employer, int $order) {
		$query = $this->getQuery($employer, $order);
		return DataTables::of($query)
			->addColumn('action', function ($answer) use ($employer) {
				// Route::get('/employers.orders.answers.edit/{answer}', 'AnswerController@edit')->name('employers.orders.answers.edit');
				$showRoute = route('employers.orders.answers.show', ['answer' => $answer->id]);
				$editRoute = route('employers.orders.answers.edit', ['answer' => $answer->id]);
				$items = [];

				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index(int $employer, int $order) {
		$query = $this->getQuery($employer, $order);
		$count = count($query);

		return view('employers.answers.index', compact('count', 'employer', 'order'));
	}

	public function create() {
		//
	}

	public function store(Request $request) {
		//
	}

	public function show(int $answer) {
		return $this->edit($answer, true);
	}

	public function edit(int $answer, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$context = session('context');
		$answer = Answer::findOrFail($answer);
		$employer = $context['employer'];
		$order = $context['order'];

		return view('employers.answers.edit', compact('mode', 'answer', 'employer', 'order'));
	}

	public function update(Request $request, int $answer) {
		$answer = Answer::findOrFail($answer);
		if ($request->has('approved'))
			$answer->update(['approved' => $request->approved]);
		if ($request->has('quantity'))
			$answer->orderSpecialty->update(['quantity' => $request->quantity]);

		$name = $answer->orderSpecialty->specialty->getTitle();
		$employer = $answer->employer->getKey();
		$order = $answer->orderSpecialty->order->getKey();

		session()->put('success', "Ответ по специальности &laquo;{$name}&raquo; в заявке на практику обновлён");
		return redirect()->route('employers.orders.answers.index', compact('employer', 'order'));
	}

	public function destroy($id) {
		//
	}
}
