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
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => $title, 'active' => true, 'context' => 'timetable', 'link' => route('timetables.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif графика стажировки
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
			['name' => 'name', 'title' => 'Наименование записи графика стажировки', 'required' => false, 'type' => 'text', 'value' => $timetable->name],
			['name' => 'internship_id', 'type' => 'hidden', 'value' => $timetable->internship->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($timetable, $mode, 'close') }}
@endsection
