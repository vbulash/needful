@extends('layouts.detail')

@section('service')
	Работа с руководителями практики
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Руководители практики', 'active' => true, 'context' => 'teacher', 'link' => route('teachers.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Новый руководитель практики
@endsection

@section('form.params')
	id="{{ form(\App\Models\Teacher::class, $mode, 'id') }}" name="{{ form(\App\Models\Teacher::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Teacher::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'name', 'title' => 'ФИО руководителя практики', 'required' => true, 'type' => 'text'],
			['name' => 'in_school', 'title' => 'Работает в учебном заведении', 'required' => false, 'type' => 'checkbox', 'value' => true],
			['name' => 'school', 'title' => 'Работает в', 'required' => false, 'type' => 'select', 'options' => $schools, 'placeholder' => 'Выберите учебное заведение'],
			['name' => 'employer', 'title' => 'Работает в', 'required' => false, 'type' => 'select', 'options' => $employers, 'placeholder' => 'Выберите работодателя'],
			['name' => 'position', 'title' => 'Должность руководителя практики', 'required' => true, 'type' => 'text'],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Teacher::class, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		let place = document.getElementById('in_school');
		place.addEventListener('change', (event) => {
			if (event.target.checked) {	// Работает в учебном заведении
				event.target.parentElement.querySelector('label').innerText = 'Работает в учебном заведении';
				document.getElementById('school').parentElement.parentElement.style.display = 'flex';
				document.getElementById('employer').parentElement.parentElement.style.display = 'none';
			} else {	// Работает у работодателя
				event.target.parentElement.querySelector('label').innerText = 'Работает у работодателя';
				document.getElementById('school').parentElement.parentElement.style.display = 'none';
				document.getElementById('employer').parentElement.parentElement.style.display = 'flex';
			}
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('employer').parentElement.parentElement.style.display = 'none';
		}, false);
	</script>
@endpush
