<?php

namespace App\Http\Controllers\planning;

use App\Http\Controllers\Controller;
use App\Events\ToastEvent;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Answer;
use App\Models\AnswerStatus;
use App\Models\Contract;
use App\Models\Order;
use App\Models\OrderEmployerStatus;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AnswerController extends Controller {
	private function getQuery(int $order, int $answer = 0) {
		$sql = <<<EOS
SELECT
	a.id AS aid,
	o.`school_id` AS school,
	a.`employer_id` AS eid,
	os.`id` AS oid,
	e.`short` AS employer,
	a.`approved`,
	a.`status`,
	a.`contract_id` AS contract,
	s.`id` AS sid,
	s.`name` AS specialty
FROM
	`orders_specialties` AS os,
	`orders_employers` AS oe,
	`answers` AS a,
	`employers` AS e,
	`specialties` AS s,
	`orders` AS o
WHERE
	a.`orders_specialties_id` = os.`id`
	AND os.`order_id` = :order
	AND a.`employer_id` = e.`id`
	AND os.`specialty_id` = s.id
	AND oe.`order_id` = os.`order_id`
	AND oe.`employer_id` = a.`employer_id`
	AND oe.`status` = :status
	AND os.`order_id` = o.`id`

EOS;
		if ($answer != 0)
			$sql .= " AND a.id = :answer";

		$params = [
			'status' => OrderEmployerStatus::ACCEPTED->value,
			'order' => $order
		];
		if ($answer != 0)
			$params['answer'] = $answer;
		return DB::select($sql, $params);
	}

	public function getData(int $order) {
		$query = $this->getQuery($order);

		return DataTables::of($query)
			->addColumn('status', fn($answer) => AnswerStatus::getName($answer->status))
			->addColumn('contract', function ($answer) {
				if (isset($answer->contract)) {
					$_contract = Contract::findOrFail($answer->contract);
					return $_contract->getTitle();
				} else
					return '';
			})
			->addColumn('action', function ($answer) use ($order) {
				$selectRoute = route('planning.answers.select', ['answer' => $answer->aid]);

				if (isset($answer->contract)) {
				} else {
					//
				}
				$items = [];

				if ($answer->status != AnswerStatus::REJECTED->value)
					$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Практиканты'];

				if ($answer->status == AnswerStatus::DONE->value) {
					if (isset($answer->contract)) {
						$contractRoute = route('contracts.show', ['contract' => $answer->contract]);
						$items[] = ['type' => 'divider'];
						$items[] = ['type' => 'item', 'link' => $contractRoute, 'icon' => 'fas fa-arrow-right', 'title' => 'Переход к договору на практику'];
						$items[] = ['type' => 'item', 'click' => "clickDetach({$answer->contract}, {$answer->aid})", 'icon' => 'fas fa-cancel', 'title' => 'Убрать специальность из договора'];
					} else {
						$createRoute = route('planning.contracts.create', [
							'order' => $order,
							'answer' => $answer->aid
						]);
						$items[] = ['type' => 'divider'];
						$items[] = ['type' => 'item', 'click' => "clickListContracts({$answer->school}, {$answer->eid}, '{$answer->specialty}')", 'icon' => 'fas fa-plus', 'title' => 'Добавить специальность в договор'];
						$items[] = ['type' => 'item', 'link' => $createRoute, 'icon' => 'fas fa-check', 'title' => 'Регистрация договора на практику'];
					}
				}

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function select(int $answer) {
		$context = session('context');
		$context['answer'] = $answer;
		session()->put('context', $context);

		return redirect()->route('planning.students.index');
	}

	public function index(int $order) {
		$context = session('context');
		unset($context['answer']);
		session()->put('context', $context);
		$query = $this->getQuery($order);
		$count = count($query);
		$ready = [];
		collect($query)->each(function ($item) use (&$ready) {
			if ($item->status == AnswerStatus::DONE->value)
				if (!isset($ready[$item->eid]))
					$ready[$item->eid] = $item->employer;
		});

		return view('planning.answers.index', compact('count', 'order', 'ready'));
	}

	public function create() {
		// $mode = config('global.create');
		// $context = session('context');
		// $school = School::findOrFail($context['school']);

		// return view('orders.create', compact('mode', 'school'));
	}

	public function store(Request $request) {
		// $context = session('context');
		// $school = School::findOrFail($context['school']);

		// $order = new Order([
		// 	'name' => $request->name,
		// 	'start' => $request->start,
		// 	'end' => $request->end
		// ]);
		// $order->school()->associate($school);
		// $order->save();

		// session()->put('success', "Заявка на практику № {$order->getKey()} создана");
		// return redirect()->route('orders.index');
	}

	public function show(Request $request, int $id) {
		$mode = config('global.show');
		$order = Order::findOrFail($id);
		return view('planning.orders.show', compact('order', 'mode'));
		// return $this->edit($request, $id, true);
	}

	public function edit(Request $request, int $id, bool $show = false) {
		// $mode = $show ? config('global.show') : config('global.edit');
		// $order = Order::findOrFail($id);
		// return view('orders.edit', compact('order', 'mode'));
	}

	public function update(UpdateOrderRequest $request, int $id) {
		// $order = Order::findOrFail($id);
		// $order->update([
		// 	'name' => $request->name,
		// 	'start' => $request->start,
		// 	'end' => $request->end,
		// 	'place' => $request->place,
		// 	'description' => $request->description,
		// ]);

		// session()->put('success', "Заявка на практику № {$order->getKey()} обновлена");
		// return redirect()->route('orders.index');
	}

	public function destroy(Request $request, int $order) {
		// if ($order == 0) {
		// 	$id = $request->id;
		// } else
		// 	$id = $order;

		// $order = Order::findOrFail($id);
		// $order->delete();

		// event(new ToastEvent('success', '', "Заявка на практику № {$id} удалена"));
		// return true;
	}
}
