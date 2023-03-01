@extends('layouts.detail')

@section('service')
	Работа с заявками на практику
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Работодатель',
		        'active' => false,
		        'context' => 'employer',
		        'link' => route('employers.index'),
		    ],
		    ['title' => 'Специальности<br/>Заявки от ОУ' . (env('BRANCH_EMPLOYER') ? '<br/>Практики работодателей' : ''), 'active' => true, 'context' => 'employer.order', 'link' => route('employers.orders.index', ['employer' => $employer])],
		];
	@endphp
@endsection

@section('interior.header')
	Просмотр заявки на практику &laquo;{{ $order->name }}&raquo;
@endsection

@section('form.params')
	id="order-show" name="order-show" action="{{ route('employers.orders.index', ['employer' => $employer]) }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'name', 'title' => 'Название практики', 'required' => true, 'type' => 'text', 'value' => $order->name];
		$fields[] = ['name' => 'start', 'title' => 'Дата начала', 'required' => true, 'type' => 'date', 'value' => $order->start->format('d.m.Y')];
		$fields[] = ['name' => 'end', 'title' => 'Дата завершения', 'required' => true, 'type' => 'date', 'value' => $order->end->format('d.m.Y')];
		$fields[] = ['name' => 'place', 'title' => 'Населённый пункт прохождения практики', 'required' => true, 'type' => 'text', 'value' => $order->place];
		$fields[] = ['name' => 'description', 'title' => 'Дополнительная информация', 'required' => false, 'type' => 'textarea', 'value' => $order->description];
	@endphp
@endsection

@section('form.close')
	{{ route('employers.orders.index', ['employer' => $employer]) }}
@endsection
