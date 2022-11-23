<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Models\Order;
use App\Models\School;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller {
	public function getData(Request $request) {
		$context = session('context');
		$school = School::findOrFail($context['school']);
		$query = $school->orders;

		return DataTables::of($query)
			->editColumn('start', fn($order) => $order->start->format('d.m.Y'))
			->editColumn('end', fn($order) => $order->end->format('d.m.Y'))
			->addColumn('action', function ($order) use ($context) {
			    $editRoute = route('orders.edit', ['order' => $order->getKey()]);
			    $showRoute = route('orders.show', ['order' => $order->getKey()]);
			    $selectRoute = route('orders.select', ['order' => $order->id]);
			    $actions = '';

			    $actions .=
			    	"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
			    	"<i class=\"fas fa-edit\"></i>\n" .
			    	"</a>\n";
			    $actions .=
			    	"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
			    	"<i class=\"fas fa-eye\"></i>\n" .
			    	"</a>\n";
			    $actions .=
			    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$order->getKey()}, '')\">\n" .
			    	"<i class=\"fas fa-trash-alt\"></i>\n" .
			    	"</a>\n";
			    $actions .=
			    	"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
			    	"<i class=\"fas fa-check\"></i>\n" .
			    	"</a>\n";

			    return $actions;
		    })
			->make(true);
	}

	public function select(int $id) {
		$context = session('context');
		$context['order'] = $id;
		session()->put('context', $context);

		//return redirect()->route('fspecialties.index', ['sid' => session()->getId()]);
	}

	public function index() {
		$context = session('context');
		$school = School::findOrFail($context['school']);
		$count = $school->orders()->count();

		return view('orders.index', compact('count'));
	}

	public function create() {
		$mode = config('global.create');
		$context = session('context');
		$school = School::findOrFail($context['school']);

		return view('orders.create', compact('mode', 'school'));
	}

	public function store(Request $request) {
		$context = session('context');
		$school = School::findOrFail($context['school']);

		$order = new Order([
			'name' => $request->name,
			'start' => $request->start,
			'end' => $request->end
		]);
		$order->school()->associate($school);
		$order->save();

		session()->put('success', "Заявка на практику № {$order->getKey()} создана");
		return redirect()->route('orders.index');
	}

	public function show(Request $request, int $id) {
		return $this->edit($request, $id, true);
	}

	public function edit(Request $request, int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$order = Order::findOrFail($id);
		return view('orders.edit', compact('order', 'mode'));
	}

	public function update(Request $request, int $id) {
		$order = Order::findOrFail($id);
		$order->update([
			'name' => $request->name,
			'start' => $request->start,
			'end' => $request->end
		]);

		session()->put('success', "Заявка на практику № {$order->getKey()} обновлена");
		return redirect()->route('orders.index');
	}

	public function destroy(Request $request, int $order) {
		if ($order == 0) {
			$id = $request->id;
		} else
			$id = $order;

		$order = Order::findOrFail($id);
		$order->delete();

		event(new ToastEvent('success', '', "Заявка на практику № {$id} удалена"));
		return true;
	}
}
