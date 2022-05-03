@extends('layouts.detail')

@section('header') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Практиканты', 'active' => true, 'context' => 'student', 'link' => route('students.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	анкеты практиканта &laquo;{{ $student->getTitle() }}&raquo;
@endsection

@section('form.params')
	id="{{ form($student, $mode, 'id') }}" name="{{ form($student, $mode, 'name') }}"
	action="{{ form($student, $mode, 'action') }}"
@endsection

@section('form.fields')
	@include('students.assign')
	@php
		$fields = [
			['name' => 'lastname', 'title' => 'Фамилия', 'required' => true, 'type' => 'text', 'value' => $student->lastname],
			['name' => 'firstname', 'title' => 'Имя', 'required' => true, 'type' => 'text', 'value' => $student->firstname],
			['name' => 'surname', 'title' => 'Отчество', 'required' => false, 'type' => 'text', 'value' => $student->surname],
			['name' => 'sex', 'title' => 'Пол', 'required' => true, 'type' => 'select', 'options' => [
				'Мужской' => 'Мужской',
				'Женский' => 'Женский',
			], 'value' => $student->sex],
			['name' => 'birthdate', 'title' => 'Дата рождения', 'required' => true, 'type' => 'date', 'value' => $student->birthdate->format('d.m.Y')],
			['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $student->phone],
			['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $student->email],
			['name' => 'parents', 'title' => 'ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать', 'required' => false, 'type' => 'textarea', 'value' => $student->parents],
			['name' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов', 'required' => false, 'type' => 'textarea', 'value' => $student->parentscontact],
			['name' => 'passport', 'title' => 'Данные паспорта (серия, номер, кем и когда выдан)', 'required' => false, 'type' => 'textarea', 'value' => $student->passport],
			['name' => 'address', 'title' => 'Адрес проживания', 'required' => false, 'type' => 'textarea', 'value' => $student->address],
			['name' => 'institutions', 'title' => 'Учебное заведение (на момент заполнения)', 'required' => false, 'type' => 'textarea', 'value' => $student->institutions],
			['name' => 'grade', 'title' => 'Класс / группа (на момент заполнения)', 'required' => false, 'type' => 'text', 'value' => $student->grade],
			['name' => 'hobby', 'title' => 'Увлечения (хобби)', 'required' => false, 'type' => 'textarea', 'value' => $student->hobby],
			['name' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?', 'required' => false, 'type' => 'number', 'value' => $student->contestachievements],
			['name' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения', 'required' => false, 'type' => 'textarea', 'value' => $student->dream],
			['name' => 'dream', 'title' => 'Чем хочется заниматься в жизни?', 'required' => false, 'type' => 'textarea', 'value' => $student->dream],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($student, $mode, 'close') }}
@endsection
