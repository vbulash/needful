@extends('layouts.detail')

@section('service')
	Работа с учебными заведениями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учебное заведение', 'active' => false, 'context' => 'school', 'link' => route('schools.index')],
			['title' => 'Специальности<br/>Заявки на практику', 'active' => true, 'context' => 'specialty', 'link' => route('fspecialties.index')],
		];
	@endphp
@endsection

@section('interior.header')
	<div>
		<p>Новая специальность в учебном заведении</p>
		@if (!auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value))
			<p>Новые специальности может добавлять только администратор платформы</p>
		@endif
	</div>
@endsection

@section('form.params')
	id="{{ form(\App\Models\Fspecialty::class, $mode, 'id') }}" name="{{ form(\App\Models\Fspecialty::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Fspecialty::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'specialty_id', 'title' => 'Выбор специальности', 'required' => false, 'type' => 'select', 'placeholder' => 'Выберите специальность'],
		];
        if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value))
			$fields[] = ['name' => 'specialty', 'title' => 'Нет в списке, добавить новую специальность', 'required' => false, 'type' => 'text'];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Fspecialty::class, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		function formatRecord(record) {
			if (!record.id) return record.text;

			if (isNaN(parseInt(record.id))) return record.text;

			return $(
				"<div class='row'>\n" +
				"<div class='col-9'>" + record.text + "</div>\n" +
				"<div class='col-3'>" + (record.federal === 1 ? "Федеральный справочник" : "Ручной ввод") + "</div>\n" +
				"</div>\n"
			);
		}

		$(document).ready(function() {
			let data = {!! $specialties !!};
			let select = $('#specialty_id');
			select.empty().select2({
				language: 'ru',
				data: data,
				templateResult: formatRecord,
			});
			select.trigger('change');
		});
	</script>
@endpush
