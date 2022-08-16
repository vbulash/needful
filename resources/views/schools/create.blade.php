@extends('layouts.detail')

@section('service')
	Работа с учебными заведениями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учебное заведение', 'active' => true, 'context' => 'school'],
			['title' => 'Специальность', 'active' => false, 'context' => 'specialty'],
		];
	@endphp
@endsection

@section('interior.header')
	Новое учебное заведение
@endsection

@section('form.params')
	id="{{ form(\App\Models\School::class, $mode, 'id') }}" name="{{ form(\App\Models\School::class, $mode, 'name') }}"
	action="{{ form(\App\Models\School::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@include('employers.assign')
	@php
		$fields = [
            ['name' => 'type', 'title' => 'Тип учебного заведения', 'required' => true, 'type' => 'select', 'options' => [
                \App\Models\SchoolType::SCHOOL->value => \App\Models\SchoolType::getName(\App\Models\SchoolType::SCHOOL->value),
                \App\Models\SchoolType::COLLEGE->value => \App\Models\SchoolType::getName(\App\Models\SchoolType::COLLEGE->value),
                \App\Models\SchoolType::UNIVERSITY->value => \App\Models\SchoolType::getName(\App\Models\SchoolType::UNIVERSITY->value),
			], 'value' => \App\Models\SchoolType::COLLEGE->value],
			['name' => 'status', 'type' => 'hidden', 'value' => \App\Models\ActiveStatus::NEW->value],
			['name' => 'short', 'title' => 'Краткое наименование организации', 'required' => true, 'type' => 'text', 'length' => 40],
			['name' => 'name', 'title' => 'Наименование организации', 'required' => true, 'type' => 'textarea'],
			['name' => 'contact', 'title' => 'Контактное лицо', 'required' => false, 'type' => 'text'],
			['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text'],
			['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'text'],
			['name' => 'inn', 'title' => 'Индивидуальный номер налогоплательщика (ИНН)', 'required' => true, 'type' => 'text'],
			['name' => 'kpp', 'title' => 'КПП', 'required' => false, 'type' => 'text'],
			['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП', 'required' => true, 'type' => 'text'],
			['name' => 'official_address', 'title' => 'Юридический адрес', 'required' => false, 'type' => 'text'],
			['name' => 'post_address', 'title' => 'Почтовый адрес', 'required' => true, 'type' => 'text'],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\School::class, $mode, 'close') }}
@endsection
