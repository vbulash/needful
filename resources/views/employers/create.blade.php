@extends('layouts.detail')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Работодатель',
		        'active' => true,
		        'context' => 'employer',
		        'link' => route('employers.index'),
		    ],
		    ['title' => 'Специальности<br/>Заявки от ОУ' . (env('BRANCH_EMPLOYER') ? '<br/>Практики работодателей' : ''), 'active' => false, 'context' => 'employer.specialty'],
		];
	@endphp
@endsection

@section('interior.header')
	Новый работодатель
@endsection

@section('form.params')
	id="{{ form(\App\Models\Employer::class, $mode, 'id') }}" name="{{ form(\App\Models\Employer::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Employer::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@include('employers.assign')
	@php
		$fields = [
		    ['name' => 'short', 'title' => 'Краткое наименование организации', 'required' => true, 'type' => 'text', 'length' => 40],
		    ['name' => 'name', 'title' => 'Наименование организации', 'required' => true, 'type' => 'text'],
		    ['name' => 'contact', 'title' => 'Контактное лицо', 'required' => false, 'type' => 'text'],
		    ['name' => 'address', 'title' => 'Фактический адрес', 'required' => false, 'type' => 'text'],
		    ['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text'],
		    ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'text'],
		    ['name' => 'inn', 'title' => 'Индивидуальный номер налогоплательщика (ИНН)', 'required' => true, 'type' => 'text'],
		    ['name' => 'kpp', 'title' => 'КПП', 'required' => false, 'type' => 'text'],
		    ['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП', 'required' => true, 'type' => 'text'],
		    ['name' => 'official_address', 'title' => 'Юридический адрес', 'required' => false, 'type' => 'text'],
		    ['name' => 'post_address', 'title' => 'Почтовый адрес', 'required' => true, 'type' => 'text'],
		    ['name' => 'status', 'type' => 'hidden', 'value' => \App\Models\ActiveStatus::NEW->value],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Employer::class, $mode, 'close') }}
@endsection
