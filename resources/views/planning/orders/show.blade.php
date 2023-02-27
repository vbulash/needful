@extends('layouts.detail')

@section('service')
	Планирование практикантов по заявкам на практику от образовательных учреждений
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Заявки на практику', 'active' => true, 'context' => 'order', 'link' => route('planning.orders.index')], ['title' => 'Ответы работодателей', 'active' => false, 'context' => 'answer']];
	@endphp
@endsection

@section('interior.header')
	Просмотр заявки на практику &laquo;{{ $order->name }}&raquo;
@endsection

@section('form.params')
	id="planning-orders-show" name="planning-orders-show"
	action=""
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
	{{ route('planning.orders.index') }}
@endsection
