@extends('layouts.detail')

@section('header') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учащиеся', 'active' => true, 'context' => 'student', 'link' => route('students.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Новый учащийся
@endsection

@section('form.params')
	id="{{ form(\App\Models\Student::class, $mode, 'id') }}" name="{{ form(\App\Models\Student::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Student::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@include('students.assign')
	@php
		$fields = [
            ['name' => 'status', 'type' => 'hidden', 'value' => \App\Models\ActiveStatus::NEW->value],
			['name' => 'lastname', 'title' => 'Фамилия', 'required' => true, 'type' => 'text'],
			['name' => 'firstname', 'title' => 'Имя', 'required' => true, 'type' => 'text'],
			['name' => 'surname', 'title' => 'Отчество', 'required' => false, 'type' => 'text'],
			['name' => 'specialties', 'type' => 'hidden', 'value' => ''],
			['name' => 'hspecialties', 'title' => 'Специальности', 'required' => true, 'type' => 'select', 'multiple' => true, 'options' => $specialties, 'placeholder' => 'Выберите одну или несколько специальностей'],
			['name' => 'sex', 'title' => 'Пол', 'required' => true, 'type' => 'select', 'options' => [
				'Мужской' => 'Мужской',
				'Женский' => 'Женский',
			]],
			['name' => 'birthdate', 'title' => 'Дата рождения', 'required' => true, 'type' => 'date'],
			['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text'],
			['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'],
			['name' => 'parents', 'title' => 'ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать', 'required' => false, 'type' => 'textarea'],
			['name' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов', 'required' => false, 'type' => 'textarea'],
			['name' => 'passport', 'title' => 'Данные паспорта (серия, номер, кем и когда выдан)', 'required' => false, 'type' => 'textarea'],
			['name' => 'address', 'title' => 'Адрес проживания', 'required' => false, 'type' => 'textarea'],
			['name' => 'institutions', 'title' => 'Учебное заведение (на момент заполнения)', 'required' => false, 'type' => 'textarea'],
			['name' => 'grade', 'title' => 'Класс / группа (на момент заполнения)', 'required' => false, 'type' => 'text'],
			['name' => 'hobby', 'title' => 'Увлечения (хобби)', 'required' => false, 'type' => 'textarea'],
			['name' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?', 'required' => false, 'type' => 'number'],
			['name' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения', 'required' => false, 'type' => 'textarea'],
			['name' => 'dream', 'title' => 'Чем хочется заниматься в жизни?', 'required' => false, 'type' => 'textarea'],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Student::class, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		let form = document.getElementById("{{ form(\App\Models\Student::class, $mode, 'id') }}");
		form.addEventListener('submit', () => {
			$('#specialties').val(JSON.stringify($('#hspecialties').val()));
		}, false);
	</script>
@endpush
