@extends('contracts.steps.wizard')

@section('service')
	Регистрация договора на практику
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'school', 'title' => 'Название образовательного учреждения', 'required' => false, 'type' => 'text', 'value' => $total['school'], 'disabled' => true];
		$fields[] = ['name' => 'employer', 'title' => 'Название работодателя', 'required' => false, 'type' => 'text', 'value' => $total['employer'], 'disabled' => true];

		$fields[] = ['title' => 'Информация по договору', 'type' => 'heading'];
		$fields[] = ['name' => 'number', 'title' => 'Номер договора', 'required' => false, 'type' => 'text', 'value' => $total['number'], 'disabled' => true];
		$fields[] = ['name' => 'sealed', 'title' => 'Дата подписания договора', 'required' => false, 'type' => 'date', 'value' => $total['sealed'], 'disabled' => true];
		$fields[] = ['name' => 'start', 'title' => 'Дата начала практики', 'required' => false, 'type' => 'date', 'value' => $total['start'], 'disabled' => true];
		$fields[] = ['name' => 'finish', 'title' => 'Дата завершения практики', 'required' => false, 'type' => 'date', 'value' => $total['finish'], 'disabled' => true];
		$fields[] = ['name' => 'scan', 'title' => 'Приложен скан договора', 'required' => false, 'type' => 'text', 'value' => $total['scan'], 'disabled' => true];
	@endphp
@endsection
