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
	Новая специальность в учебном заведении
@endsection

@section('form.params')
	id="{{ form(\App\Models\Fspecialty::class, $mode, 'id') }}" name="{{ form(\App\Models\Fspecialty::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Fspecialty::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'specialty_id', 'title' => 'Выбор специальности', 'required' => false, 'type' => 'select', 'placeholder' => 'Выберите специальность'],
			['name' => 'specialty', 'title' => 'Нет в списке, добавить новую специальность', 'required' => false, 'type' => 'text'],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Fspecialty::class, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		$(document).ready(function() {
			//$('#specialty_id').select2('destroy');
			// TODO затем сделать повторную инициализацию с нужными параметрами
		});
	</script>
@endpush
