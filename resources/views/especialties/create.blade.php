@extends('layouts.detail')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Практика', 'active' => false, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => 'Специальности для практики', 'active' => true, 'context' => 'especialty', 'link' => route('especialties.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	<div>
		<p>Новая специальность по практике работодателя</p>
		@if (!auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value))
			<p>Новые специальности может добавлять только администратор платформы</p>
		@endif
	</div>
@endsection

@section('form.params')
	id="{{ form(\App\Models\Especialty::class, $mode, 'id') }}" name="{{ form(\App\Models\Especialty::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Especialty::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'specialty_id', 'title' => 'Выбор специальности', 'required' => false, 'type' => 'select', 'placeholder' => 'Выберите специальность'],
		];
        if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value))
			$fields[] = ['name' => 'specialty', 'title' => 'Нет в списке, добавить новую специальность', 'required' => false, 'type' => 'text'];
        $fields[] = ['name' => 'count', 'title' => 'Количество позиций', 'required' => true, 'type' => 'number', 'min' => 1];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Especialty::class, $mode, 'close') }}
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
