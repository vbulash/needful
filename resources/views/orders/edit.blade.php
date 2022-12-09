@extends('layouts.detail')

@section('service')
	Работа с заявками на практику
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Заявки на практику', 'active' => true, 'context' => 'order', 'link' => route('orders.index')],
			['title' => 'Специальности в заявке', 'active' => false, 'context' => 'order.specialty']
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	заявки на практику &laquo;{{ $order->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($order, $mode, 'id') }}" name="{{ form($order, $mode, 'name') }}"
	action="{{ form($order, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'name', 'title' => 'Название практики', 'required' => true, 'type' => 'text', 'value' => $order->name];
		$fields[] = ['name' => 'start', 'title' => 'Дата начала', 'required' => true, 'type' => 'date', 'value' => $order->start->format('d.m.Y')];
		$fields[] = ['name' => 'end', 'title' => 'Дата завершения', 'required' => true, 'type' => 'date', 'value' => $order->end->format('d.m.Y')];
		$fields[] = ['name' => 'description', 'title' => 'Дополнительная информация', 'required' => false, 'type' => 'textarea', 'value' => $order->description];
	@endphp
@endsection

@section('form.close')
	{{ form($order, $mode, 'close') }}
@endsection
