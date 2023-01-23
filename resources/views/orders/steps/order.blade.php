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

		$field = ['name' => 'place', 'title' => 'Населённый пункт прохождения практики', 'required' => true, 'type' => 'text'];
		if (isset($heap['place']))
			$field['value'] = $heap['place'];
		$fields[] = $field;

		$field = ['name' => 'description', 'title' => 'Дополнительная информация', 'required' => false, 'type' => 'textarea'];
		if (isset($heap['description']))
			$field['value'] = $heap['description'];
		$fields[] = $field;
	@endphp
@endsection

