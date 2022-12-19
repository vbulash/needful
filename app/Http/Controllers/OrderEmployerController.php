<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreOrderEmployerRequest;
use App\Http\Requests\UpdateOrderEmployerRequest;
use App\Http\Requests\UpdateOrderSpecialtyRequest;
use App\Models\Employer;
use App\Models\Order;
use App\Models\OrderEmployer;
use App\Models\OrderEmployerStatus;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderEmployerController extends Controller
{
	public function getData(int $order) {
		$query = Order::findOrFail($order)->employers();

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
			    $name = $order_employer->getTitle();
			    $employer_id = $order_employer->employer->getKey();
			    $actions = '';

			    $actions .=
			    	"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
			    	"<i class=\"fas fa-eye\"></i>\n" .
			    	"</a>\n";
			    $actions .=
			    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" " .
			    	"onclick=\"clickDelete({$id}, '{$name}', {$employer_id})\">\n" .
			    	"<i class=\"fas fa-trash-alt\"></i>\n" .
			    	"</a>\n";
			    $actions .=
			    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Сообщение работодателю\" " .
			    	"onclick=\"clickMail({$id}, '{$name}', {$employer_id})\">\n" .
			    	"<i class=\"fas fa-envelope\"></i>\n" .
			    	"</a>\n";

			    return $actions;
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
		$name = $order_employer->getTitle();
		$order_employer->delete();

		event(new ToastEvent('success', '', "Работодатель &laquo;{$name}&raquo; удалён из заявки на практику"));
		return true;
	}
}
