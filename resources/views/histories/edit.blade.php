@extends('layouts.detail')

@section('service')
	@if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value))
		Мои практики
	@else
		Работа с практиками
	@endif
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Практика', 'active' => true, 'context' => 'history', 'link' => route('history.index')],
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
	записи истории практики № {{ $history->getKey() }}
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
			['name' => 'internship', 'title' => 'Практика', 'required' => false, 'type' => 'text', 'value' => $history->timetable->internship->getTitle(), 'disabled' => true],
			['name' => 'timetable', 'title' => 'График практики', 'required' => false, 'type' => 'text', 'value' => $history->timetable->getTitle(), 'disabled' => true],
			['name' => 'teacher', 'title' => 'Руководитель практики', 'required' => false, 'type' => 'text', 'value' => $teacher, 'disabled' => true],
			['name' => 'trainees', 'title' => 'Количество практикантов (утверждены / запланировано)', 'required' => false, 'type' => 'text',
				'value' => $history->students()->wherePivot('status', \App\Models\TraineeStatus::APPROVED->value)->count() . ' / ' . $history->timetable->planned, 'disabled' => true],
			['name' => 'status', 'title' => 'Статус практики', 'required' => true, 'type' => 'select', 'value' => $history->status, 'options' => [
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
