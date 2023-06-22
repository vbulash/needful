<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\UpdateOrderSpecialtyRequest;
use App\Models\Order;
use App\Models\OrderSpecialty;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderSpecialtyController extends Controller {
	public function getData(int $order) {
		$query = Order::findOrFail($order)->specialties();

		return DataTables::of($query)
			->editColumn('id', fn($order_specialty) => $order_specialty->specialty->getKey())
			->addColumn('specialty', fn($order_specialty) => $order_specialty->specialty->getTitle())
			->addColumn('action', function ($order_specialty) use ($order) {
				$editRoute = route('order.specialties.edit', [
					'order' => $order,
					'specialty' => $order_specialty->getKey()
				]);
				$showRoute = route('order.specialties.show', [
					'order' => $order,
					'specialty' => $order_specialty->getKey()
				]);
				$id = $order_specialty->specialty->getKey();
				$name = $order_specialty->specialty->getTitle();
				$items = [];

				if (auth()->user()->can('orders.details.edit'))
					$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];
				if (auth()->user()->can('orders.details.show'))
					$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				if (auth()->user()->can('orders.details.delete'))
					$items[] = ['type' => 'item', 'click' => "clickDelete({$order_specialty->getKey()}, '{$name}', {$id})", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index(int $order) {
		$context = session('context');
		unset($context['order.specialty']);
		session()->put('context', $context);

		$_order = Order::findOrFail($order);
		$count = $_order->specialties()->count();
		$selected = [];
		$_order->specialties()->each(function ($specialty) use (&$selected) {
			$id = $specialty->specialty->getKey();
			$text = $specialty->specialty->getTitle();
			$selected[$id] = $text;
		});
		$enabled = [];
		$_order->school->specialties()->each(function ($specialty) use (&$enabled, $selected) {
			$id = $specialty->specialty->getKey();
			$text = $specialty->specialty->getTitle();
			if (array_key_exists($id, $selected))
				return;
			$enabled[$id] = $text;
		});
		return view('orders.specialties.index', compact('count', 'order', 'selected', 'enabled'));
	}

	public function create(Request $request, int $order) {
		//
	}

	public function store(Request $request, int $order) {
		$id = $request->id;
		$text = $request->text;
		$quantity = $request->quantity;

		$orderSpecialty = new OrderSpecialty();
		$orderSpecialty->quantity = $quantity;
		$orderSpecialty->order()->associate($order);
		$orderSpecialty->specialty()->associate($id);
		$orderSpecialty->save();

		event(new ToastEvent('success', '', "Специальность &laquo;{$text}&raquo; добавлена в заявку на практику"));
		return true;
	}

	public function show(int $order, int $specialty) {
		return $this->edit($order, $specialty, true);
	}

	public function edit(int $order, int $specialty, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$specialty = OrderSpecialty::findOrFail($specialty);
		return view('orders.specialties.edit', compact('mode', 'order', 'specialty'));
	}

	public function update(UpdateOrderSpecialtyRequest $request, int $order, int $specialty) {
		$specialty = OrderSpecialty::findOrFail($specialty);
		$specialty->update([
			'quantity' => $request->quantity
		]);
		$name = $specialty->specialty->getTitle();

		session()->put('success', "Специальность &laquo;{$name}&raquo; в заявке на практику обновлена");
		return $this->index($order);
	}

	public function destroy(Request $request, int $order, int $specialty) {
		if ($specialty == 0) {
			$id = $request->id;
		} else
			$id = $specialty;

		$specialty = OrderSpecialty::findOrFail($id);
		$name = $specialty->specialty->getTitle();
		$specialty->delete();

		event(new ToastEvent('success', '', "Специальность &laquo;{$name}&raquo; удалена из заявки на практику"));
		return true;
	}
}
