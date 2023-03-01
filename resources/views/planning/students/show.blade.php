@extends('layouts.detail')

@section('service')
	Планирование практикантов по заявкам на практику от образовательных учреждений
@endsection

@section('steps')
	@php
		$context = session('context');
		$steps = [['title' => 'Заявки на практику', 'active' => false, 'context' => 'order', 'link' => route('planning.orders.index')], ['title' => 'Ответы работодателей', 'active' => false, 'context' => 'answer', 'link' => route('planning.answers.index', ['order' => $context['order']])], ['title' => 'Практиканты', 'active' => true, 'context' => 'answer.students', 'link' => route('planning.students.index')]];
	@endphp
@endsection

@section('interior.header')
	Просмотр анкеты учащегося &laquo;{{ $student->getTitle() }}&raquo;
@endsection

@section('form.params')
	id="show-student" name="show-student"
	action=""
@endsection

@section('form.fields')
	@php
		$fields = [];
		
		$fields[] = ['name' => 'lastname', 'title' => 'Фамилия', 'required' => true, 'type' => 'text', 'value' => $student->lastname];
		$fields[] = ['name' => 'firstname', 'title' => 'Имя', 'required' => true, 'type' => 'text', 'value' => $student->firstname];
		$fields[] = ['name' => 'surname', 'title' => 'Отчество', 'required' => false, 'type' => 'text', 'value' => $student->surname];
		
		$fields[] = [
		    'name' => 'sex',
		    'title' => 'Пол',
		    'required' => true,
		    'type' => 'select',
		    'options' => [
		        'Мужской' => 'Мужской',
		        'Женский' => 'Женский',
		    ],
		    'value' => $student->sex,
		];
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
	{{ route('planning.students.index') }}
@endsection

@push('js_after')
	<script>
		let form = document.getElementById("show-form");
		form.addEventListener('submit', () => {
			//
		}, false);
	</script>
@endpush
