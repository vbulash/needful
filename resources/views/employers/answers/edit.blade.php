@extends('layouts.detail')

@section('service')
	Работа с работодателями
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
		    [
		        'title' => 'Заявки на практику',
		        'active' => false,
		        'context' => 'order',
		        'link' => route('employers.orders.index', compact('employer')),
		    ],
		    [
		        'title' => 'Ответы на заявку',
		        'active' => true,
		        'context' => 'answer',
		    ],
		];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.edit'))
		Редактирование
	@else
		Просмотр
	@endif ответа работодателя
@endsection

@section('form.params')
	id="{{ form($answer, $mode, 'id') }}" name="{{ form($answer, $mode, 'name') }}"
	action="{{ form($answer, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'name', 'title' => 'Наименование специальности', 'required' => false, 'type' => 'text', 'value' => $answer->orderSpecialty->specialty->getTitle(), 'disabled' => true];
		$fields[] = ['name' => 'quantity', 'title' => 'Количество практикантов в заявке', 'required' => false, 'type' => 'text', 'value' => $answer->orderSpecialty->quantity, 'disabled' => true];
		$fields[] = ['name' => 'approved', 'title' => 'Готовы принять практикантов', 'required' => true, 'type' => 'number', 'value' => $answer->approved, 'min' => 0, 'max' => $answer->orderSpecialty->quantity];
	@endphp
@endsection

@section('form.close')
	{{ route('employers.orders.answers.index', ['employer' => $employer, 'order' => $order]) }}
@endsection
