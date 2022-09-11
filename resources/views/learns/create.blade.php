@extends('layouts.detail')

@section('header') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учащийся', 'active' => false, 'context' => 'student', 'link' => route('students.index', ['sid' => session()->getId()])],
			['title' => 'История обучения', 'active' => true, 'context' => 'learn', 'link' => route('learns.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Новая запись истории обучения<br/>
	<small>Оставьте дату завершения незаполненной для текущего (последнего) учебного заведения</small>
@endsection

@section('form.params')
	id="{{ form(\App\Models\Learn::class, $mode, 'id') }}" name="{{ form(\App\Models\Learn::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Learn::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
            ['name' => 'status', 'type' => 'hidden', 'value' => \App\Models\ActiveStatus::NEW->value],
            ['name' => 'start', 'title' => 'Дата поступления', 'required' => true, 'type' => 'date'],
            ['name' => 'finish', 'title' => 'Дата завершения', 'required' => false, 'type' => 'date'],
            ['title' => 'Учебное заведение', 'type' => 'heading'],
			['name' => 'school_id', 'title' => 'Учебное заведение', 'required' => false, 'type' => 'select', 'options' => $schools, 'placeholder' => 'Выберите учебное заведение'],
			['name' => 'new_school', 'title' => 'Новое учебное заведение (нет в списке)', 'required' => false, 'type' => 'text'],
			['title' => 'Специальность', 'type' => 'heading'],
			['name' => 'specialty_id', 'title' => 'Специальность', 'required' => false, 'type' => 'select', 'options' => $specialties, 'placeholder' => 'Выберите специальность'],
			['name' => 'new_specialty', 'title' => 'Новая специальность (нет в списке)', 'required' => false, 'type' => 'text'],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Learn::class, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		let form = document.getElementById("{{ form(\App\Models\Learn::class, $mode, 'id') }}");
		form.addEventListener('submit', () => {
			//$('#specialties').val(JSON.stringify($('#hspecialties').val()));
		}, false);
	</script>
@endpush
