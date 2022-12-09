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
		<p>
		@if($mode == config('global.show'))
			Просмотр
		@else
			Изменение
		@endif специальности &laquo;{{ $especialty->specialty->name }}&raquo;
		</p>
		@if($mode == config('global.edit'))
			@if (!auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value))
				<p>Новые специальности может добавлять только администратор платформы</p>
			@endif
		@endif
	</div>
@endsection

@section('form.params')
	id="{{ form($especialty, $mode, 'id') }}" name="{{ form($especialty, $mode, 'name') }}"
	action="{{ form($especialty, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
        if ($mode == config('global.show')) {
			if ($especialty->specialty->federal) {
				$fields[] = [
					'name' => 'federal', 'title' => 'Тип записи специальности', 'required' => false, 'type' => 'text', 'disabled' => true, 'value' => 'Специальность из федерального справочника'
				];
			} else {
				$fields[] = [
					'name' => 'federal', 'title' => 'Тип записи специальности', 'required' => false, 'type' => 'text', 'disabled' => true, 'value' => 'Специальность введена вручную'
				];
			}
        }
		$fields[] = [
			'name' => 'specialty_id', 'title' => 'Выбор специальности', 'required' => false, 'type' => 'select'
		];
        $fields[] = [
            'name' => 'id', 'type' => 'hidden', 'value' => $especialty->getKey()
		];
        if($mode == config('global.edit'))
            if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value))
            	$fields[] = ['name' => 'specialty', 'title' => 'Нет в списке, добавить новую специальность', 'required' => false, 'type' => 'text'];
        $fields[] = ['name' => 'count', 'title' => 'Количество позиций', 'required' => true, 'type' => 'number', 'min' => 1, 'value' => $especialty->count];
	@endphp
@endsection

@section('form.close')
	{{ form($especialty, $mode, 'close') }}
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

		$(document).ready(function () {
			let data = {!! $specialties !!};
			let select = $('#specialty_id');
			select.empty().select2({
				language: 'ru',
				data: data,
				templateResult: formatRecord,
			});
			select.val({{ $especialty->specialty_id }});
			select.trigger('change');
		});
	</script>
@endpush
