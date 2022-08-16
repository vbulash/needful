@extends('layouts.detail')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Наставник', 'active' => true, 'context' => 'teacher', 'link' => route('teachers.index', ['sid' => session()->getId()])],
			['title' => 'Практиканты', 'active' => false, 'context' => 'tstudent'],
		];
	@endphp
@endsection

@section('interior.header')
	Новый наставник
@endsection

@section('form.params')
	id="{{ form(\App\Models\Teacher::class, $mode, 'id') }}" name="{{ form(\App\Models\Teacher::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Teacher::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'name', 'title' => 'ФИО наставника', 'required' => true, 'type' => 'text'],
			['name' => 'in_school', 'title' => 'Работает в учебном заведении', 'required' => false, 'type' => 'checkbox', 'value' => true],
			['title' => 'В зависимости от значения переключателя будет выбор из списка учебных заведений или работодателей', 'type' => 'heading'],
			['name' => 'school', 'title' => 'Работает в', 'required' => false, 'type' => 'select', 'options' => $schools],
			['name' => 'employer', 'title' => 'Работает в', 'required' => false, 'type' => 'select', 'options' => $employers],
			['name' => 'position', 'title' => 'Должность наставника', 'required' => true, 'type' => 'text'],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Teacher::class, $mode, 'close') }}
@endsection
