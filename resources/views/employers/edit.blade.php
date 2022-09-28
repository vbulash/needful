@extends('layouts.detail')

@section('service')
	Работа с работодателями
	@if (isset(session('context')['chain']))
		(только цепочка значений)
	@endif
@endsection

@section('steps')
	@php
		if (isset(session('context')['chain']))
            $title = 'График стажировки';
        else
            $title = 'График стажировки или Специальности для стажировки';
		$steps = [
			['title' => 'Работодатель', 'active' => true, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship'],
			['title' => $title, 'active' => false, 'context' => 'timetable'],
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
		$fields = [];
		if (auth()->user()->hasRole('Администратор')) {
			$fields[] = ['name' => 'status', 'title' => 'Статус активности объекта', 'required' => false, 'type' => 'select', 'options' => [
				\App\Models\ActiveStatus::NEW->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::NEW->value),
				\App\Models\ActiveStatus::ACTIVE->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::ACTIVE->value),
				\App\Models\ActiveStatus::FROZEN->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::FROZEN->value),
			], 'value' => $employer->status];
		}
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
	{{ form($employer, $mode, 'close') }}
@endsection
