@extends('layouts.detail')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => 'Специальности для стажировки', 'active' => true, 'context' => 'especialty', 'link' => route('especialties.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Изменение
	@endif специальности &laquo;{{ $especialty->specialty->name }}&raquo;
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

		$(document).ready(function() {
			let data = {!! $specialties !!};
			let select = $('#specialty_id');
			select.select2('destroy');
			select.select2({
				language: 'ru',
				data: data,
				templateResult: formatRecord,
			});
			select.val({{ $especialty->specialty_id }});
			select.trigger('change');
		});
	</script>
@endpush
