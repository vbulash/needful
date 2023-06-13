@php
	if ($answer != 0) {
		$_answer = App\Models\Answer::findOrFail($answer);
	}
@endphp

@extends('layouts.detail')

@section('service')
	Планирование практикантов по заявкам на практику от образовательных учреждений
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Заявки на практику',
		        'active' => false,
		        'context' => 'order',
		        'link' => route('planning.orders.index'),
		    ],
		    [
		        'title' => 'Ответы работодателей',
		        'active' => false,
		        'context' => 'answer',
				'link' => route('planning.answers.index', ['order' => $order]),
		    ],
		    [
				'title' => 'Договор практики', 'active' => true, 'context' => ''],
		];
	@endphp
@endsection

@section('interior.header')
	Регистрация договора на практику с работодателем &laquo;{{ $employer->getTitle() }}&raquo;
	@if ($answer != 0)
		по специальности &laquo;{{ $_answer->orderSpecialty->specialty->name }}&raquo;
	@endif
@endsection

@section('form.params')
	id="contract-create" name="contract-create" action="{{ route('planning.contracts.store') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'number', 'title' => 'Номер договора', 'required' => true, 'type' => 'text'];
		$fields[] = ['name' => 'sealed', 'title' => 'Дата подписания договора', 'required' => true, 'type' => 'date'];
		$fields[] = ['name' => 'start_fake', 'title' => 'Дата начала практики', 'required' => false, 'type' => 'text', 'value' => $start->format('d.m.Y'), 'disabled' => 'true'];
		$fields[] = ['name' => 'finish_fake', 'title' => 'Дата завершения практики', 'required' => false, 'type' => 'text', 'value' => $finish->format('d.m.Y'), 'disabled' => 'true'];
		$fields[] = ['title' => ' ', 'type' => 'heading'];
		$fields[] = ['name' => 'scan', 'title' => 'Скан договора', 'required' => false, 'type' => 'file'];

		$fields[] = ['name' => 'employer', 'type' => 'hidden', 'value' => $employer->getKey()];
		$fields[] = ['name' => 'school', 'type' => 'hidden', 'value' => $school->getKey()];
		$fields[] = ['name' => 'answer', 'type' => 'hidden', 'value' => $answer];
		$fields[] = ['name' => 'order', 'type' => 'hidden', 'value' => $order];
		$fields[] = ['name' => 'start', 'type' => 'hidden', 'value' => $start->format('Y-m-d')];
		$fields[] = ['name' => 'finish', 'type' => 'hidden', 'value' => $finish->format('Y-m-d')];
	@endphp
@endsection

@section('form.close')
	{{ route('planning.answers.index', ['order' => $order]) }}
@endsection
