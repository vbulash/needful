@extends('layouts.detail')

@section('service')
	Работа с заявками на практику
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Заявки на практику', 'active' => false, 'context' => 'order', 'link' => route('orders.index')],
			['title' => 'Специальности в заявке', 'active' => true, 'context' => 'order.specialty', 'link' => route('order.specialties.index', ['order' => $order])]
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	специальности &laquo;{{ $specialty->getTitle() }}&raquo; в заявке на практику
@endsection

@section('form.params')
	id="{{ form($specialty, $mode, 'id') }}" name="{{ form($specialty, $mode, 'name') }}"
	action="{{ form($specialty, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'name', 'title' => 'Название специальности', 'required' => true, 'type' => 'text', 'value' => $specialty->getTitle(), 'disabled' => true];
		$fields[] = ['name' => 'quantity', 'title' => 'Количество позиций', 'required' => true, 'type' => 'number', 'value' => $specialty->quantity, 'min' => 1];
	@endphp
@endsection

@section('form.close')
	{{ form($specialty, $mode, 'close') }}
@endsection
