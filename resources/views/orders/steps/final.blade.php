@extends('orders.steps.wizard')

@section('service')
	Создание заявки на практику
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'name', 'title' => 'Название образовательного учреждения', 'required' => false, 'type' => 'text', 'value' => $total['school']];
		$fields[] = ['title' => 'Информация по заявке', 'type' => 'heading'];
		$fields[] = ['name' => 'name', 'title' => 'Название практики', 'required' => false, 'type' => 'text', 'value' => $total['name']];
		$fields[] = ['name' => 'start', 'title' => 'Дата начала', 'required' => false, 'type' => 'date', 'value' => $total['start']->format('d.m.Y')];
		$fields[] = ['name' => 'end', 'title' => 'Дата завершения', 'required' => false, 'type' => 'date', 'value' => $total['end']->format('d.m.Y')];
		$fields[] = ['name' => 'place', 'title' => 'Населённый пункт прохождения практики', 'required' => false, 'type' => 'text', 'value' => $total['place']];
		$fields[] = ['name' => 'description', 'title' => 'Дополнительная информация', 'required' => false, 'type' => 'textarea', 'value' => $total['description']];
		$fields[] = ['title' => 'Список специальностей в заявке', 'type' => 'heading'];
		$fields[] = ['name' => 'specialties', 'title' => 'Специальности, количество позиций по специальности указано в скобках', 'required' => false, 'type' => 'textarea', 'value' => $total['specialties']];
		$fields[] = ['title' => 'Список работодателя для уведомления о заявке на практику (для рассылки писем)', 'type' => 'heading'];
		$fields[] = ['name' => 'employers', 'title' => 'Список работодателей', 'required' => false, 'type' => 'textarea', 'value' => $total['employers']];
	@endphp
@endsection
