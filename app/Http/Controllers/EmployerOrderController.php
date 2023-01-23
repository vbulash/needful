<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Models\Employer;
use App\Models\Order;
use App\Models\OrderEmployer;
use App\Models\OrderEmployerStatus;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployerOrderController extends Controller {
	public function getData(int $employer) {
		$query = Employer::findOrFail($employer)->orders;
		// ->wherePivotNotIn('status', [OrderEmployerStatus::REJECTED->value]);

		return DataTables::of($query)
			->addColumn('start', fn($order) => $order->start->format('d.m.Y'))
			->addColumn('end', fn($order) => $order->end->format('d.m.Y'))
			->editColumn('status', fn($order) => OrderEmployerStatus::getName($order->pivot->status))
			->addColumn('action', function ($order) use ($employer) {
				$showRoute = route('employers.orders.show', ['employer' => $employer, 'order' => $order->getKey()]);
				$answerRoute = route('employers.orders.select', ['employer' => $employer, 'order' => $order->getKey()]);
				$items = [];

				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				if ($order->pivot->status != OrderEmployerStatus::REJECTED->value) {
					$items[] = ['type' => 'item', 'click' => "clickCancel({$employer}, {$order->getKey()}, '{$order->getTitle()}')", 'icon' => 'fas fa-ban', 'title' => 'Отмена'];
				}
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'link' => $answerRoute, 'title' => 'Ответы работодателя'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index(int $employer) {
		$count = Employer::findOrFail($employer)->orders()->count();
		return view('employers.orders.index', compact('employer', 'count'));
	}

	public function select(int $employer, int $order) {
		$order = Order::findOrFail($order);
		$context = session('context');
		$context['order'] = $order->getKey();
		session()->put('context', $context);

		return redirect()->route('employers.orders.answers.index', ['employer' => $employer, 'order' => $order->getKey()]);
	}

	public function show(int $employer, int $order) {
		$mode = config('global.show');
		$order = Order::findOrFail($order);
		return view('employers.orders.show', compact('employer', 'order', 'mode'));
	}

	public function cancel(Request $request) {
		$employer = Employer::findOrFail($request->employer);
		$employer->orders()->updateExistingPivot($request->order, [
			'status' => OrderEmployerStatus::REJECTED->value,
		]);
		$order = Order::findOrFail($request->order);

		event(new ToastEvent('success', '', "Приглашение работодателя на заявку &laquo;{$order->getTitle()}&raquo; отменено"));
		return true;
	}
}
