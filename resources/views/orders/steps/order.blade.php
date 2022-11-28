@extends('orders.steps.wizard')

@section('service')
	Создание заявки на практику
@endsection

@section('form.fields')
	@php
        $fields = [];

		$field = ['name' => 'name', 'title' => 'Название практики', 'required' => true, 'type' => 'text'];
		if (isset($heap['name']))
			$field['value'] = $heap['name'];
		$fields[] = $field;

		$field = ['name' => 'start', 'title' => 'Дата начала', 'required' => true, 'type' => 'date'];
		if (isset($heap['start']))
			$field['value'] = $heap['start']->format('d.m.Y');
		$fields[] = $field;

		$field = ['name' => 'end', 'title' => 'Дата завершения', 'required' => true, 'type' => 'date'];
		if (isset($heap['end']))
			$field['value'] = $heap['end']->format('d.m.Y');
		$fields[] = $field;
	@endphp
@endsection

