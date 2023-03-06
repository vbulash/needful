@extends('layouts.detail')

@section('service')
	Работа с руководителями практики
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Руководители практики', 'active' => true, 'context' => 'teacher', 'link' => route('teachers.index', ['sid' => session()->getId()])]];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	анкеты руководителя практики &laquo;{{ $teacher->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($teacher, $mode, 'id') }}" name="{{ form($teacher, $mode, 'name') }}"
	action="{{ form($teacher, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'name', 'title' => 'ФИО руководителя практики', 'required' => true, 'type' => 'text', 'value' => $teacher->name];
		
		$select = $teacher->job->getKey();
		switch ($teacher->job->getMorphClass()) {
		    case \App\Models\Employer::class:
		        $title = 'Работает у работодателя';
		        $fields[] = ['name' => 'in_school', 'title' => $title, 'required' => false, 'type' => 'checkbox', 'value' => false];
		        $fields[] = ['name' => 'employer', 'title' => 'Работает в', 'required' => false, 'type' => 'select', 'options' => $employers, 'value' => $select];
		        $fields[] = ['name' => 'school', 'title' => 'Работает в', 'required' => false, 'type' => 'select', 'options' => $schools];
		        break;
		    case \App\Models\School::class:
		        $title = 'Работает в образовательном учреждении';
		        $fields[] = ['name' => 'in_school', 'title' => $title, 'required' => false, 'type' => 'checkbox', 'value' => true];
		        $fields[] = ['name' => 'employer', 'title' => 'Работает в', 'required' => false, 'type' => 'select', 'options' => $employers];
		        $fields[] = ['name' => 'school', 'title' => 'Работает в', 'required' => false, 'type' => 'select', 'options' => $schools, 'value' => $select];
		        break;
		}
		$fields[] = ['name' => 'position', 'title' => 'Должность руководителя практики', 'required' => true, 'type' => 'text', 'value' => $teacher->position];
		$fields[] = ['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $teacher->phone];
		$fields[] = ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $teacher->email];
	@endphp
@endsection

@section('form.close')
	{{ form($teacher, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		let place = document.getElementById('in_school');
		place.addEventListener('change', (event) => {
			if (event.target.checked) { // Работает в образовательном учреждении
				event.target.parentElement.querySelector('label').innerText = 'Работает в образовательном учреждении';
				document.getElementById('school').parentElement.parentElement.style.display = 'flex';
				document.getElementById('employer').parentElement.parentElement.style.display = 'none';
			} else { // Работает у работодателя
				event.target.parentElement.querySelector('label').innerText = 'Работает у работодателя';
				document.getElementById('school').parentElement.parentElement.style.display = 'none';
				document.getElementById('employer').parentElement.parentElement.style.display = 'flex';
			}
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			place.dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
