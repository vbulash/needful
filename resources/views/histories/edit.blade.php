@extends('layouts.detail')

@section('history') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Истории стажировки', 'active' => true, 'context' => 'history', 'link' => route('history.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	записи истории стажировок № {{ $history->getKey() }}
@endsection

@section('form.params')
	id="{{ form($history, $mode, 'id') }}" name="{{ form($history, $mode, 'name') }}"
	action="{{ form($history, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'employer', 'title' => 'Работодатель', 'required' => false, 'type' => 'text', 'value' => $history->timetable->internship->employer->getTitle(), 'disabled' => true],
			['name' => 'internship', 'title' => 'Стажировка', 'required' => false, 'type' => 'text', 'value' => $history->timetable->internship->getTitle(), 'disabled' => true],
			['name' => 'timetable', 'title' => 'График стажировки', 'required' => false, 'type' => 'text', 'value' => $history->timetable->getTitle(), 'disabled' => true],
			['name' => 'student', 'title' => 'Практикант', 'required' => false, 'type' => 'text', 'value' => $history->student->getTitle(), 'disabled' => true],
			['name' => 'status', 'title' => 'Статус стажировки', 'required' => true, 'type' => 'select', 'value' => $history->status, 'options' => [
                'Планируется' => 'Планируется',
                'Выполняется' => 'Выполняется',
                'Закрыта' => 'Закрыта',
            ]],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($history, $mode, 'close') }}
@endsection
