@extends('layouts.detail')

@section('service')
	Работа с договорами на практику
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Договор на практику',
		        'active' => true,
		        'context' => 'contract',
		        'link' => route('contracts.index'),
		    ],
		];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	договора на практику &laquo;{{ $contract->getTitle() }}&raquo;
@endsection

@section('form.params')
	id="contract-edit" name="contract-edit"
	action=""
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'school', 'title' => 'Название образовательного учреждения', 'required' => false, 'type' => 'text', 'value' => $contract->school->getTitle(), 'disabled' => true];
		$fields[] = ['name' => 'employer', 'title' => 'Название работодателя', 'required' => false, 'type' => 'text', 'value' => $contract->employer->getTitle(), 'disabled' => true];

		$fields[] = ['title' => 'Информация по договору', 'type' => 'heading'];
		$fields[] = ['name' => 'number', 'title' => 'Номер договора', 'required' => false, 'type' => 'text', 'value' => $contract->number];
		$fields[] = ['name' => 'sealed', 'title' => 'Дата подписания договора', 'required' => false, 'type' => 'date', 'value' => $contract->sealed->format('d.m.Y')];
		$fields[] = ['name' => 'start', 'title' => 'Дата начала практики', 'required' => false, 'type' => 'date', 'value' => $contract->start->format('d.m.Y')];
		$fields[] = ['name' => 'finish', 'title' => 'Дата завершения практики', 'required' => false, 'type' => 'date', 'value' => $contract->finish->format('d.m.Y')];
		// $fields[] = ['name' => 'scan', 'title' => 'Приложен скан договора', 'required' => false, 'type' => 'text', 'value' => $total['scan'], 'disabled' => true];
	@endphp
@endsection

@section('form.close')
	{{ route('contracts.index') }}
@endsection
