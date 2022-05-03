@extends('layouts.detail')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => 'График стажировки', 'active' => true, 'context' => 'timetable', 'link' => route('timetables.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Новый график стажировки
@endsection

@section('form.params')
	id="{{ form(\App\Models\Timetable::class, $mode, 'id') }}" name="{{ form(\App\Models\Timetable::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Timetable::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'start', 'title' => 'Начало', 'required' => true, 'type' => 'date', 'placeholder' => 'Выберите дату'],
			['name' => 'end', 'title' => 'Завершение', 'required' => true, 'type' => 'date', 'placeholder' => 'Выберите дату'],
			['name' => 'name', 'title' => 'Наименование записи графика стажировки', 'required' => false, 'type' => 'text'],
			['name' => 'internship_id', 'type' => 'hidden', 'value' => $internship->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Timetable::class, $mode, 'close') }}
@endsection
