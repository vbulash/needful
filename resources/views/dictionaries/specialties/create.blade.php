@extends('layouts.detail')

@section('header')<div class="mt-4"></div>@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Специальность', 'active' => false, 'context' => 'specialty', 'link' => route('specialties.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Новая специальность
@endsection

@section('form.params')
	id="{{ form(\App\Models\Specialty::class, $mode, 'id') }}" name="{{ form(\App\Models\Specialty::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Specialty::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
            ['name' => 'federal', 'title' => 'Тип записи справочника', 'required' => false, 'type' => 'text', 'disabled' => true, 'value' => 'Ручной ввод (не принадлежит к федеральному справочнику)'],
			['name' => 'name', 'title' => 'Название специальности', 'required' => true, 'type' => 'text'],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Specialty::class, $mode, 'close') }}
@endsection
