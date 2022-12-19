@extends('layouts.detail')

@section('service')
	Работа с заявками на практику
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Заявки на практику', 'active' => false, 'context' => 'order', 'link' => route('orders.index')],
			['title' => 'Уведомления работодателей', 'active' => true, 'context' => 'order.employer', 'link' => route('order.employers.index', ['order' => $order])]
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	работодателя &laquo;{{ $employer->getTitle() }}&raquo; в заявке на практику
@endsection

@section('form.params')
	id="{{ form($order_employer, $mode, 'id') }}" name="{{ form($order_employer, $mode, 'name') }}"
	action="{{ form($order_employer, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'short', 'title' => 'Краткое наименование организации', 'required' => true, 'type' => 'text', 'length' => 40, 'value' => $employer->short];
		$fields[] = ['name' => 'name', 'title' => 'Наименование организации', 'required' => true, 'type' => 'text', 'value' => $employer->name];
		$fields[] = ['name' => 'contact', 'title' => 'Контактное лицо', 'required' => false, 'type' => 'text', 'value' => $employer->contact];
		$fields[] = ['name' => 'address', 'title' => 'Фактический адрес', 'required' => false, 'type' => 'text', 'value' => $employer->address];
		$fields[] = ['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $employer->phone];
		$fields[] = ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'text', 'value' => $employer->email];
		$fields[] = ['name' => 'inn', 'title' => 'Индивидуальный номер налогоплательщика (ИНН)', 'required' => true, 'type' => 'text', 'value' => $employer->inn];
		$fields[] = ['name' => 'kpp', 'title' => 'КПП', 'required' => false, 'type' => 'text', 'value' => $employer->kpp];
		$fields[] = ['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП', 'required' => true, 'type' => 'text', 'value' => $employer->ogrn];
		$fields[] = ['name' => 'official_address', 'title' => 'Юридический адрес', 'required' => false, 'type' => 'text', 'value' => $employer->official_address];
		$fields[] = ['name' => 'post_address', 'title' => 'Почтовый адрес', 'required' => true, 'type' => 'text', 'value' => $employer->post_address];
	@endphp
@endsection

@section('form.close')
	{{ form($order_employer, $mode, 'close') }}
@endsection
