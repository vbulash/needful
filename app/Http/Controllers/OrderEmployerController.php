<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderEmployerRequest;
use App\Http\Requests\UpdateOrderEmployerRequest;
use App\Models\OrderEmployer;

class OrderEmployerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderEmployerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderEmployerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderEmployer  $orderEmployer
     * @return \Illuminate\Http\Response
     */
    public function show(OrderEmployer $orderEmployer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderEmployer  $orderEmployer
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderEmployer $orderEmployer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderEmployerRequest  $request
     * @param  \App\Models\OrderEmployer  $orderEmployer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderEmployerRequest $request, OrderEmployer $orderEmployer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderEmployer  $orderEmployer
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderEmployer $orderEmployer)
    {
        //
    }
}
