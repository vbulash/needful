<?php

namespace App\Http\Controllers\planning;

use App\Http\Controllers\Controller;
use App\Events\ToastEvent;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\School;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller {
	public function getData(Request $request) {
		$query = Order::all();

		return DataTables::of($query)
			->addColumn('school', fn($order) => $order->school->getTitle())
			->addColumn('start', fn($order) => $order->start->format('d.m.Y'))
			->addColumn('end', fn($order) => $order->end->format('d.m.Y'))
			->addColumn('action', function ($order) {
				$showRoute = route('planning.orders.show', ['order' => $order->getKey()]);
				$selectRoute = route('planning.orders.select', ['order' => $order->getKey()]);

				$items = [];

				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Ответы работодателей'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function select(int $order) {
		$context = ['order' => $order];
		session()->put('context', $context);

		return redirect()->route('planning.answers.index', ['order' => $order]);
	}

	public function index() {
		session()->forget('context');
		$count = Order::all()->count();

		return view('planning.orders.index', compact('count'));
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