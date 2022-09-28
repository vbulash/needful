@extends('layouts.detail')

@section('header')<div class="mt-4"></div>@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Стажировки', 'active' => true, 'context' => 'history', 'link' => route('history.index', ['sid' => session()->getId()])],
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
