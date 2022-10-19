@extends('layouts.detail')

@section('service')
	@if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value))
		Мои стажировки
	@else
		Работа со стажировками
	@endif
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Стажировка', 'active' => true, 'context' => 'history', 'link' => route('history.index', ['sid' => session()->getId()])],
			['title' => 'Практиканты', 'active' => false, 'context' => 'trainee'],
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
            ['name' => 'history', 'type' => 'hidden', 'value' => $history->getKey()],
			['name' => 'employer', 'title' => 'Работодатель', 'required' => false, 'type' => 'text', 'value' => $history->timetable->internship->employer->getTitle(), 'disabled' => true],
			['name' => 'internship', 'title' => 'Стажировка', 'required' => false, 'type' => 'text', 'value' => $history->timetable->internship->getTitle(), 'disabled' => true],
			['name' => 'timetable', 'title' => 'График стажировки', 'required' => false, 'type' => 'text', 'value' => $history->timetable->getTitle(), 'disabled' => true],
			['name' => 'trainees', 'title' => 'Количество практикантов (подвердили участие / планируются)', 'required' => false, 'type' => 'text',
				'value' => $history->students()->wherePivot('status', \App\Models\TraineeStatus::ACCEPTED->value)->count() . ' / ' . $history->timetable->planned, 'disabled' => true],
			['name' => 'status', 'title' => 'Статус стажировки', 'required' => true, 'type' => 'select', 'value' => $history->status, 'options' => [
                \App\Models\HistoryStatus::NEW->value => \App\Models\HistoryStatus::getName(\App\Models\HistoryStatus::NEW->value),
                \App\Models\HistoryStatus::PLANNED->value => \App\Models\HistoryStatus::getName(\App\Models\HistoryStatus::PLANNED->value),
                \App\Models\HistoryStatus::ACTIVE->value => \App\Models\HistoryStatus::getName(\App\Models\HistoryStatus::ACTIVE->value),
                \App\Models\HistoryStatus::CLOSED->value => \App\Models\HistoryStatus::getName(\App\Models\HistoryStatus::CLOSED->value),
            ]],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($history, $mode, 'close') }}
@endsection
