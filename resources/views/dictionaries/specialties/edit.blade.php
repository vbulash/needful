@extends('layouts.detail')

@section('service') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Специальность', 'active' => false, 'context' => 'specialty', 'link' => route('specialties.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif специальности &laquo;{{ $specialty->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($specialty, $mode, 'id') }}" name="{{ form($specialty, $mode, 'name') }}"
	action="{{ form($specialty, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		if ($specialty->federal) {
			$fields = [
				['name' => 'federal', 'title' => 'Тип записи справочника', 'required' => false, 'type' => 'text', 'disabled' => true, 'value' => 'Запись федерального справочника'],
				['name' => 'order', 'title' => 'Номер по порядку', 'required' => false, 'type' => 'text', 'value' => $specialty->order],
				['name' => 'code', 'title' => 'Код', 'required' => false, 'type' => 'text', 'value' => $specialty->code],
				['name' => 'name', 'title' => 'Название специальности', 'required' => true, 'type' => 'text', 'value' => $specialty->name],
				['name' => 'degree', 'title' => 'Квалификация', 'required' => false, 'type' => 'text', 'value' => $specialty->degree],
				['name' => 'level0', 'title' => 'Уровень 0 (Тип профессии)', 'required' => false, 'type' => 'text', 'value' => isset($specialty->level0) ? $specialty->level0->name : ''],
				['name' => 'level1', 'title' => 'Уровень 1 (Отрасль экономики)', 'required' => false, 'type' => 'text', 'value' => isset($specialty->level1) ? $specialty->level1->name : ''],
				['name' => 'level2', 'title' => 'Уровень 2 (Специализация в отрасли)', 'required' => false, 'type' => 'text', 'value' => isset($specialty->level2) ? $specialty->level2->name : ''],
			];
        } else {
            $fields = [
				['name' => 'federal', 'title' => 'Тип записи справочника', 'required' => false, 'type' => 'text', 'disabled' => true, 'value' => 'Ручной ввод (не принадлежит к федеральному справочнику)'],
				['name' => 'name', 'title' => 'Название специальности', 'required' => true, 'type' => 'text', 'value' => $specialty->name],
			];
        }
	@endphp
@endsection

@section('form.close')
	{{ form($specialty, $mode, 'close') }}
@endsection
