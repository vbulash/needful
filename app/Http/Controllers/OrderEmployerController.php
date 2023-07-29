<?php

namespace App\Http\Controllers;

use App\Events\orders\New2SentTaskEvent;
use App\Events\ToastEvent;
use App\Events\UnreadCountEvent;
use App\Http\Requests\StoreOrderEmployerRequest;
use App\Http\Requests\UpdateOrderEmployerRequest;
use App\Http\Requests\UpdateOrderSpecialtyRequest;
use App\Models\Answer;
use App\Models\Employer;
use App\Models\Order;
use App\Models\OrderEmployer;
use App\Models\OrderEmployerStatus;
use App\Notifications\orders\New2Sent;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Yajra\DataTables\DataTables;

class OrderEmployerController extends Controller {
	public function getData(int $order) {
		$query = Order::findOrFail($order)->employers()
			->get();
		if (auth()->user()->cannot('orders.employers.list'))
			$query = $query->filter(fn($value, $key) => auth()->user()->isAllowed($value->employer));

		return DataTables::of($query)
			->editColumn('id', fn($order_employer) => $order_employer->employer->getKey())
			->addColumn('name', fn($order_employer) => $order_employer->getTitle())
			->addColumn('status', fn($order_employer) => OrderEmployerStatus::getName($order_employer->status))
			->addColumn('action', function ($order_employer) use ($order) {
				$showRoute = route('order.employers.show', [
					'order' => $order,
					'employer' => $order_employer->getKey()
				]);
				$id = $order_employer->getKey();
				$name = base64_encode($order_employer->getTitle());
				$employer_id = $order_employer->employer->getKey();
				$items = [];

				if (auth()->user()->can('orders.employers.edit'))
					$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр анкеты работодателя'];
				if (auth()->user()->can('orders.employers.delete')) {
					$func = sprintf("clickDelete(%d, '%s', %d)", $id, $name, $employer_id);
					$items[] = ['type' => 'item', 'click' => "{$func}", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление уведомления работодателю'];
				}

				if (count($items) > 0)
					$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'click' => "clickMail({$id})", 'icon' => 'fas fa-envelope', 'title' => 'Сообщение работодателю'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index(int $order) {
		$context = session('context');
		unset($context['order.employer']);
		session()->put('context', $context);

		$_order = Order::findOrFail($order);
		$count = $_order->employers()->count();
		$selected = [];
		$_order->employers()->each(function ($order_employer) use (&$selected) {
			$id = $order_employer->employer->getKey();
			$text = $order_employer->employer->getTitle();
			$selected[$id] = $text;
		});
		$enabled = [];
		Employer::all()->each(function ($employer) use (&$enabled, $selected) {
			$id = $employer->getKey();
			$text = $employer->getTitle();
			if (array_key_exists($id, $selected))
				return;
			$enabled[$id] = $text;
		});
		return view('orders.employers.index', compact('count', 'order', 'selected', 'enabled'));
	}

	public function create(Request $request, int $order) {
		//
	}

	public function store(StoreOrderEmployerRequest $request, int $order) {
		$id = $request->id;
		$text = $request->text;

		$orderEmployer = new OrderEmployer();
		$orderEmployer->order()->associate($order);
		$orderEmployer->employer()->associate($id);
		$orderEmployer->status = OrderEmployerStatus::NEW ->value;
		$orderEmployer->save();

		foreach ($orderEmployer->order->specialties as $orderSpecialty) {
			// foreach (OrderSpecialty::all() as $orderSpecialty) {
			$answer = new Answer();
			$answer->approved = 0;
			$answer->orderSpecialty()->associate($orderSpecialty);
			$answer->employer()->associate($orderEmployer->employer);
			$answer->save();
		}

		$orderEmployer->employer->user->allow($orderEmployer->order);

		event(new ToastEvent('success', '', "Работодатель &laquo;{$text}&raquo; добавлен в заявку на практику"));
		return true;
	}

	public function show(int $order, int $employer) {
		$mode = config('global.show');
		$order_employer = OrderEmployer::findOrFail($employer);
		$employer = $order_employer->employer;
		return view('orders.employers.edit', compact('mode', 'order', 'order_employer', 'employer'));
	}

	public function edit(int $order, int $employer) {
	}

	public function update(UpdateOrderEmployerRequest $request, int $order, int $employer) {
	}

	public function destroy(Request $request, int $order, int $employer) {
		if ($employer == 0) {
			$id = $request->id;
		} else
			$id = $employer;

		$order_employer = OrderEmployer::findOrFail($id);
		$order_employer->employer->user->disallow($order_employer->order);
		$name = $order_employer->getTitle();
		$order_employer->delete();

		event(new ToastEvent('success', '', "Работодатель &laquo;{$name}&raquo; удалён из заявки на практику"));
		return true;
	}

	public function mail(Request $request) {
		$repeat = $request->has('repeat');
		$orderEmployer = OrderEmployer::findOrFail($request->order_employer);
		$orderEmployer->employer->user->notify(new New2Sent($orderEmployer->order));
		$orderEmployer->update(['status' => OrderEmployerStatus::SENT->value]);
		event(new New2SentTaskEvent($orderEmployer));

		if ($repeat) {
			$name = $orderEmployer->employer->getTitle();
			event(new ToastEvent('success', '', "Переслано повторное уведомление о практике для работодателя &laquo;{$name}&raquo;"));
		}
		return true;
	}
}
