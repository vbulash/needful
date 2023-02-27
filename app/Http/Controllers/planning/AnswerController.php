<?php

namespace App\Http\Controllers\planning;

use App\Http\Controllers\Controller;
use App\Events\ToastEvent;
use App\Http\Requests\UpdateOrderRequest;
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
	a.id as aid,
    e.id as eid,
    os.id as oid,
    e.short as employer,
    s.id as sid,
	s.name as specialty,
	a.approved
FROM
    answers AS a,
    orders_specialties AS os,
    orders_employers AS oe,
    employers as e,
    specialties AS s
WHERE
    a.orders_specialties_id = os.id
    AND os.specialty_id = s.id
    AND oe.employer_id = e.id
    AND oe.status = :status
	AND os.order_id = :order
EOS;
		if ($answer != 0)
			$sql .= " AND a.answer = :answer";

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
			->addColumn('action', function ($answer) {
				$showRoute = route('dashboard');
				$items = [];

				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				// $items[] = ['type' => 'divider'];
				// $items[] = ['type' => 'item', 'link' => $specialtiesRoute, 'icon' => 'fas fa-graduation-cap', 'title' => 'Детали заявки'];
				// $items[] = ['type' => 'item', 'link' => $employersRoute, 'icon' => 'fas fa-building', 'title' => 'Уведомления работодателям'];
	
				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function select(Request $request, int $id) {
		// $context = ['order' => $id];
		// session()->put('context', $context);

		// return redirect()->route('dashboard', ['order' => $id]);
	}

	public function index(int $order) {
		$context = session('context');
		unset($context['answer']);
		session()->put('context', $context);
		$count = count($this->getQuery($order));

		return view('planning.answers.index', compact('count', 'order'));
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
		$order = Order::findOrFail($id);
		$order->update([
			'name' => $request->name,
			'start' => $request->start,
			'end' => $request->end,
			'place' => $request->place,
			'description' => $request->description,
		]);

		session()->put('success', "Заявка на практику № {$order->getKey()} обновлена");
		return redirect()->route('orders.index');
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