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
		    ['title' => 'Заявки на практику', 'active' => false, 'context' => 'order'],
		    [
		        'title' => 'Ответы на заявку',
		        'active' => true,
		        'context' => 'answer',
		    ],
		];
	@endphp
@endsection

@section('interior.header')
	Редактирование ответа работодателя по специальности &laquo;{{ $answer->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($answer, $mode, 'id') }}" name="{{ form($answer, $mode, 'name') }}"
	action="{{ form($answer, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'name', 'title' => 'Наименование специальности', 'required' => false, 'type' => 'text', 'value' => $answer->name, 'disabled' => true];
		$fields[] = ['name' => 'quantity', 'title' => 'Количество практикантов в заявке', 'required' => false, 'type' => 'text', 'value' => $answer->quantity, 'disabled' => true];
		$fields[] = ['name' => 'approved', 'title' => 'Готовы принять практикантов', 'required' => true, 'type' => 'number', 'value' => $answer->approved];
	@endphp
@endsection

@section('form.close')
	{{ route('employers.orders.answers.index', ['employer' => $employer, 'order' => $order]) }}
@endsection
