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
			$title = 'График практики';
		else
			$title = 'График практики или Специальности для практики';
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Практика', 'active' => false, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => $title, 'active' => true, 'context' => 'timetable', 'link' => route('timetables.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif графика практики
@endsection

@section('form.params')
	id="{{ form($timetable, $mode, 'id') }}" name="{{ form($timetable, $mode, 'name') }}"
	action="{{ form($timetable, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'start', 'title' => 'Начало', 'required' => true, 'type' => 'date', 'value' => $timetable->start->format('d.m.Y')],
			['name' => 'end', 'title' => 'Завершение', 'required' => true, 'type' => 'date', 'value' => $timetable->end->format('d.m.Y')],
			['name' => 'name', 'title' => 'Наименование записи графика практики', 'required' => false, 'type' => 'text', 'value' => $timetable->name],
			['name' => 'planned', 'title' => 'Требуется практикантов', 'required' => true, 'type' => 'number', 'min' => 1, 'value' => $timetable->planned],
			['name' => 'internship_id', 'type' => 'hidden', 'value' => $timetable->internship->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($timetable, $mode, 'close') }}
@endsection
