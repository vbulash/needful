@extends('contracts.steps.wizard')

@section('service')
	Регистрация договора на практику
@endsection

@section('form.fields')
	@php
		$fields = [];

		$field = ['name' => 'number', 'title' => 'Номер договора', 'required' => true, 'type' => 'text'];
		if (isset($heap['number'])) {
		    $field['value'] = $heap['number'];
		}
		$fields[] = $field;

		$field = ['name' => 'sealed', 'title' => 'Дата подписания договора', 'required' => true, 'type' => 'date'];
		if (isset($heap['sealed'])) {
		    $field['value'] = $heap['sealed']->format('d.m.Y');
		}
		$fields[] = $field;

		$field = ['name' => 'start', 'title' => 'Дата начала практики', 'required' => true, 'type' => 'date'];
		if (isset($heap['start'])) {
		    $field['value'] = $heap['start']->format('d.m.Y');
		}
		$fields[] = $field;

		$field = ['name' => 'finish', 'title' => 'Дата завершения практики', 'required' => true, 'type' => 'date'];
		if (isset($heap['finish'])) {
		    $field['value'] = $heap['finish']->format('d.m.Y');
		}
		$fields[] = $field;

		$fields[] = ['title' => ' ', 'type' => 'heading'];
		$field = ['name' => 'scan', 'title' => 'Скан договора', 'required' => false, 'type' => 'file'];
		$fields[] = $field;
	@endphp
@endsection
