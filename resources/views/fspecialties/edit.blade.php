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
	<div>
		<p>
			@if($mode == config('global.show'))
				Просмотр
			@else
				Изменение
			@endif специальности &laquo;{{ $fspecialty->specialty->name }}&raquo;
		</p>
		@if($mode == config('global.edit'))
			@if (!auth()->user()->hasRole('Администратор'))
				<p>Новые специальности может добавлять только администратор платформы</p>
			@endif
		@endif
	</div>
@endsection

@section('form.params')
	id="{{ form($fspecialty, $mode, 'id') }}" name="{{ form($fspecialty, $mode, 'name') }}"
	action="{{ form($fspecialty, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
        if ($mode == config('global.show')) {
			if ($fspecialty->specialty->federal) {
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
            'name' => 'id', 'type' => 'hidden', 'value' => $fspecialty->getKey()
		];
        if($mode == config('global.edit'))
            if (auth()->user()->hasRole('Администратор'))
            	$fields[] = ['name' => 'specialty', 'title' => 'Нет в списке, добавить новую специальность', 'required' => false, 'type' => 'text'];
	@endphp
@endsection

@section('form.close')
	{{ form($fspecialty, $mode, 'close') }}
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
			select.val({{ $fspecialty->specialty_id }});
			select.trigger('change');
		});
	</script>
@endpush
