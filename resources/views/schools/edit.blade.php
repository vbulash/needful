@extends('layouts.detail')

@section('service')
	Работа с учебными заведениями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учебное заведение', 'active' => true, 'context' => 'school', 'link' => route('schools.index', ['sid' => session()->getId()])],
			['title' => 'Специальность', 'active' => false, 'context' => 'specialty'],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	анкеты учебного заведения &laquo;{{ $school->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($school, $mode, 'id') }}" name="{{ form($school, $mode, 'name') }}"
	action="{{ form($school, $mode, 'action') }}"
@endsection

@section('form.fields')
	@include('schools.assign')
	@php
		$fields = [];
        if (auth()->user()->hasRole('Администратор')) {
            $fields[] = ['name' => 'status', 'title' => 'Статус активности объекта', 'required' => false, 'type' => 'select', 'options' => [
                \App\Models\ActiveStatus::NEW->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::NEW->value),
                \App\Models\ActiveStatus::ACTIVE->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::ACTIVE->value),
                \App\Models\ActiveStatus::FROZEN->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::FROZEN->value),
			], 'value' => $school->status];
        }
		$fields[] = [
            'name' => 'type', 'title' => 'Тип учебного заведения', 'required' => true, 'type' => 'select', 'options' => [
                \App\Models\SchoolType::SCHOOL->value => \App\Models\SchoolType::getName(\App\Models\SchoolType::SCHOOL->value),
                \App\Models\SchoolType::COLLEGE->value => \App\Models\SchoolType::getName(\App\Models\SchoolType::COLLEGE->value),
                \App\Models\SchoolType::UNIVERSITY->value => \App\Models\SchoolType::getName(\App\Models\SchoolType::UNIVERSITY->value),
			], 'value' => $school->type
		];

        $fields[] = ['name' => 'short', 'title' => 'Краткое наименование организации', 'required' => true, 'type' => 'text', 'length' => 40, 'value' => $school->short];
		$fields[] = ['name' => 'name', 'title' => 'Наименование организации', 'required' => true, 'type' => 'textarea', 'value' => $school->name];
		$fields[] = ['name' => 'contact', 'title' => 'Контактное лицо', 'required' => false, 'type' => 'text', 'value' => $school->contact];
		$fields[] = ['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $school->phone];
		$fields[] = ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'text', 'value' => $school->email];
		$fields[] = ['name' => 'inn', 'title' => 'Индивидуальный номер налогоплательщика (ИНН)', 'required' => true, 'type' => 'text', 'value' => $school->inn];
		$fields[] = ['name' => 'kpp', 'title' => 'КПП', 'required' => false, 'type' => 'text', 'value' => $school->kpp];
		$fields[] = ['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП', 'required' => true, 'type' => 'text', 'value' => $school->ogrn];
		$fields[] = ['name' => 'official_address', 'title' => 'Юридический адрес', 'required' => false, 'type' => 'text', 'value' => $school->official_address];
		$fields[] = ['name' => 'post_address', 'title' => 'Почтовый адрес', 'required' => true, 'type' => 'text', 'value' => $school->post_address];
	@endphp
@endsection

@section('form.close')
	{{ form($school, $mode, 'close') }}
@endsection
