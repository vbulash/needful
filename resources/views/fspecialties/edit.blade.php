@extends('layouts.detail')

@section('service')
	Работа с учебными заведениями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учебное заведение', 'active' => false, 'context' => 'school', 'link' => route('schools.index', ['sid' => session()->getId()])],
			['title' => 'Специальность', 'active' => true, 'context' => 'specialty', 'link' => route('fspecialties.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Изменение
	@endif специальности &laquo;{{ $fspecialty->specialty->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($fspecialty, $mode, 'id') }}" name="{{ form($fspecialty, $mode, 'name') }}"
	action="{{ form($fspecialty, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'specialty_id', 'title' => 'Выбор специальности', 'required' => false, 'type' => 'select', 'options' => $specialties, 'value' => $fspecialty->specialty_id],
			['name' => 'id', 'type' => 'hidden', 'value' => $fspecialty->getKey()]
		];
        if($mode == config('global.edit'))
            $fields[] = ['name' => 'specialty', 'title' => 'Нет в списке, добавить новую специальность', 'required' => false, 'type' => 'text'];
	@endphp
@endsection

@section('form.close')
	{{ form($fspecialty, $mode, 'close') }}
@endsection
