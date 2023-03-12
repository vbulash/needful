<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Models\Answer;
use App\Models\AnswerStatus;
use App\Models\Employer;
use App\Models\OrderEmployerStatus;
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
	a.approved,
	a.status
FROM
    answers AS a,
    orders_specialties AS os,
    specialties AS s,
	orders_employers AS oe
WHERE
    a.orders_specialties_id = os.id
        AND os.specialty_id = s.id
		AND os.order_id = :order1
		AND a.employer_id = :employer1
		AND oe.order_id = :order2
		AND oe.employer_id = :employer2
		AND oe.status <> :status
SQL,
			[
				'order1' => $order,
				'employer1' => $employer,
				'order2' => $order,
				'employer2' => $employer,
				'status' => OrderEmployerStatus::NEW ->value
			]);
	}

	public function getData(int $employer, int $order) {
		$_employer = Employer::find($employer);
		$_order = $_employer->orders()->find($order);
		$query = $this->getQuery($employer, $order);
		return DataTables::of($query)
			->addColumn('status', fn($answer) => AnswerStatus::getName($answer->status))
			->addColumn('action', function ($answer) use ($employer, $_order) {
				// $showRoute = route('employers.orders.answers.show', ['answer' => $answer->id]);
				$editRoute = route('employers.orders.answers.edit', ['answer' => $answer->id]);
				$selectRoute = route('employers.orders.answers.select', ['answer' => $answer->id]);
				$items = [];

				if ($_order->pivot->status == OrderEmployerStatus::SENT->value) {
					$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];
					// $items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				}

				if ($answer->approved > 0 && $_order->pivot->status == OrderEmployerStatus::ACCEPTED->value)
					$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Выбор практикантов'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index(int $employer, int $order) {
		$context = session('context');
		unset($context['answer']);
		session()->put('context', $context);

		$query = $this->getQuery($employer, $order);
		$count = count($query);
		$zeros = 0;
		foreach ($query as $answer)
			if ($answer->approved == 0)
				$zeros++;

		return view('employers.answers.index', compact('count', 'employer', 'order', 'zeros'));
	}

	public function select(int $answer) {
		$context = session('context');
		$context['answer'] = $answer;
		session()->put('context', $context);

		return redirect()->route('employers.students.index');
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