@extends('layouts.detail')

@section('header') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учащиеся', 'active' => true, 'context' => 'student', 'link' => route('students.index', ['sid' => session()->getId()])],
			['title' => 'История обучения', 'active' => false, 'context' => 'learn'],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	анкеты учащегося &laquo;{{ $student->getTitle() }}&raquo;
@endsection

@section('form.params')
	id="{{ form($student, $mode, 'id') }}" name="{{ form($student, $mode, 'name') }}"
	action="{{ form($student, $mode, 'action') }}"
@endsection

@section('form.fields')
	@if (isset(session('context')['chain']))
		@include('students.assign')
	@endif
	@php
		$fields = [];
        if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)) {
            $fields[] = ['name' => 'status', 'title' => 'Статус активности объекта', 'required' => false, 'type' => 'select', 'options' => [
                \App\Models\ActiveStatus::NEW->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::NEW->value),
                \App\Models\ActiveStatus::ACTIVE->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::ACTIVE->value),
                \App\Models\ActiveStatus::FROZEN->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::FROZEN->value),
			], 'value' => $student->status];
        } else $fields[] = ['name' => 'status', 'type' => 'hidden', 'value' => $student->status];
		$fields[] = ['name' => 'lastname', 'title' => 'Фамилия', 'required' => true, 'type' => 'text', 'value' => $student->lastname];
		$fields[] = ['name' => 'firstname', 'title' => 'Имя', 'required' => true, 'type' => 'text', 'value' => $student->firstname];
		$fields[] = ['name' => 'surname', 'title' => 'Отчество', 'required' => false, 'type' => 'text', 'value' => $student->surname];

		$fields[] = ['name' => 'sex', 'title' => 'Пол', 'required' => true, 'type' => 'select', 'options' => [
			'Мужской' => 'Мужской',
			'Женский' => 'Женский',
		], 'value' => $student->sex];
		$fields[] = ['name' => 'birthdate', 'title' => 'Дата рождения', 'required' => true, 'type' => 'date', 'value' => $student->birthdate->format('d.m.Y')];
		$fields[] = ['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $student->phone];
		$fields[] = ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $student->email];
		$fields[] = ['name' => 'parents', 'title' => 'ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать', 'required' => false, 'type' => 'textarea', 'value' => $student->parents];
		$fields[] = ['name' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов', 'required' => false, 'type' => 'textarea', 'value' => $student->parentscontact];
		$fields[] = ['name' => 'passport', 'title' => 'Данные документа, удостоверяющего личность (серия, номер, кем и когда выдан)', 'required' => false, 'type' => 'textarea', 'value' => $student->passport];
		$fields[] = ['name' => 'address', 'title' => 'Адрес проживания', 'required' => false, 'type' => 'textarea', 'value' => $student->address];
		$fields[] = ['name' => 'grade', 'title' => 'Класс / группа (на момент заполнения)', 'required' => false, 'type' => 'text', 'value' => $student->grade];
		$fields[] = ['name' => 'hobby', 'title' => 'Увлечения (хобби)', 'required' => false, 'type' => 'textarea', 'value' => $student->hobby];
		$fields[] = ['name' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?', 'required' => false, 'type' => 'number', 'value' => $student->hobbyyears];
		$fields[] = ['name' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения', 'required' => false, 'type' => 'textarea', 'value' => $student->contestachievements];
		$fields[] = ['name' => 'dream', 'title' => 'Чем хочется заниматься в жизни?', 'required' => false, 'type' => 'textarea', 'value' => $student->dream];
	@endphp
@endsection

@section('form.close')
	{{ form($student, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		let form = document.getElementById("{{ form($student, $mode, 'id') }}");
		form.addEventListener('submit', () => {
			//
		}, false);
	</script>
@endpush
