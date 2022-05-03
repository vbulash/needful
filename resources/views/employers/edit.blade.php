@extends('layouts.detail')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => true, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship'],
			['title' => 'График стажировки', 'active' => false, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	анкеты работодателя &laquo;{{ $employer->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($employer, $mode, 'id') }}" name="{{ form($employer, $mode, 'name') }}"
	action="{{ form($employer, $mode, 'action') }}"
@endsection

@section('form.fields')
	@include('employers.assign')
	@php
		$fields = [
			['name' => 'name', 'title' => 'Наименование организации', 'required' => true, 'type' => 'text', 'value' => $employer->name],
			['name' => 'contact', 'title' => 'Контактное лицо', 'required' => false, 'type' => 'text', 'value' => $employer->contact],
			['name' => 'address', 'title' => 'Фактический адрес', 'required' => false, 'type' => 'text', 'value' => $employer->address],
			['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $employer->phone],
			['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'text', 'value' => $employer->email],
			['name' => 'inn', 'title' => 'Индивидуальный номер налогоплательщика (ИНН)', 'required' => true, 'type' => 'text', 'value' => $employer->inn],
			['name' => 'kpp', 'title' => 'КПП', 'required' => false, 'type' => 'text', 'value' => $employer->kpp],
			['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП', 'required' => false, 'type' => 'text', 'value' => $employer->ogrn],
			['name' => 'official_address', 'title' => 'Юридический адрес', 'required' => false, 'type' => 'text', 'value' => $employer->official_address],
			['name' => 'post_address', 'title' => 'Почтовый адрес', 'required' => true, 'type' => 'text', 'value' => $employer->post_address],
			['name' => 'description', 'title' => 'Краткое описание организации (основная деятельность)', 'required' => false, 'type' => 'textarea', 'value' => $employer->description],
			['name' => 'expectation', 'title' => 'Какие результаты ожидаются от практикантов / выпускников?', 'required' => false, 'type' => 'textarea', 'value' => $employer->expectation],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($employer, $mode, 'close') }}
@endsection
