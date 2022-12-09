<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderSpecialty;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderSpecialtyController extends Controller
{
	public function getData(int $order) {
		$query = Order::findOrFail($order)->specialties();

		return DataTables::of($query)
			->addColumn('specialty', fn($order_specialty) => $order_specialty->specialty->getTitle())
			->addColumn('action', function ($order_specialty) use ($order) {
			    $name = $order_specialty->specialty->getTitle();
			    $actions = '';

			    $actions .=
			    	"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-1\" " .
			    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$order}, '{$name}')\">\n" .
			    	"<i class=\"fas fa-trash-alt\"></i>\n" .
			    	"</a>\n";

			    return $actions;
		    })
			->make(true);
	}

    public function index(int $order)
    {
		$context = session('context');
		unset($context['order.specialty']);
		session()->put('context', $context);

		$count = Order::findOrFail($order)->specialties()->count();
		return view('orders.specialties.index', compact('count', 'order'));
    }

    public function create(int $order)
    {
        //
    }

    public function store(Request $request, int $order)
    {
        //
    }

    public function show(int $order, int $specialty)
    {
		return $this->edit($order, $specialty, true);
    }

    public function edit(int $order, int $specialty, bool $show = false)
    {
		$mode = $show ? config('global.show') : config('global.edit');
		$specialty = OrderSpecialty::findOrFail($specialty);
		return view('orders.specialties.edit', compact('mode', 'order', 'specialty'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
