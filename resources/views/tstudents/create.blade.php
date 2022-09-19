@extends('layouts.detail')

@section('service')
	Работа с руководителями практики
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Руководители практики', 'active' => false, 'context' => 'teacher', 'link' => route('teachers.index', ['sid' => session()->getId()])],
			['title' => 'Практиканты', 'active' => true, 'context' => 'tstudent', 'link' => route('tstudents.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Новая запись прикрепления практиканта
	@if ($teacher->job->getMorphClass() == \App\Models\School::class)
		<br/>
		<small>К руководителю практики от учебного заведения могут быть привязаны только текущие учащиеся данного учебного заведения!</small>
	@endif
@endsection

@section('form.params')
	id="tstudent-create" name="tstudent-create"
	action="{{ route('tstudents.store', ['sid' => session()->getId()]) }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'specialty', 'title' => 'Специальность практиканта', 'required' => false, 'type' => 'select', 'options' => $specialties, 'placeholder' => 'Выберите специальность'],
			['name' => 'student', 'title' => 'Выбор практиканта', 'required' => true, 'type' => 'select'],	// Заполняется в отдельном handler'е
		];
	@endphp
@endsection

@section('form.close')
	{{ route('tstudents.index', ['sid' => session()->getId()]) }}
@endsection

@push('js_after')
	<script>
		$('#specialty').change((event) => {
			$.post({
				url: "{{ route('tstudents.source', ['sid' => $sid]) }}",
				data: {
					specialty: $('#specialty').val(),
					@if ($teacher->job->getMorphClass() == \App\Models\School::class)
					school: {{ $teacher->job->getKey() }},
					@endif
				},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: (data) => {
					if (data === undefined) {
						$('#student').parent().parent().hide();
						return;
					}

					let students = JSON.parse("[" + data + "]");
					$('#student').val(null);
					$('#student').select2('destroy');
					$('#student').html('');
					$('#student').select2({
						data: students,
						language: 'ru',
						theme: 'bootstrap-5'
					});
					$('#student').parent().parent().show();
				}
			});
		});

		$(document).ready(function() {
			$('#specialty').change();
		});
	</script>
@endpush
