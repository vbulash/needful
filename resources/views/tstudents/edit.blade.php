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
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	привязки практиканта
@endsection

@section('form.params')
	id="tstudent-edit" name="tstudent-edit"
	action="{{ route('tstudents.update', ['tstudent' => $learn->getKey(), 'sid' => session()->getId()]) }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
        if ($mode == config('global.edit'))
            $fields[] = [
                'name' => 'specialty', 'title' => 'Специальность практиканта', 'required' => false,
                'type' => 'select', 'options' => $specialties, 'value' => $learn->specialty->getKey()
                ];
        $fields[] = [
            'name' => 'student', 'title' => 'Выбор практиканта', 'required' => true, 'type' => 'select', 'options' => $learns, 'value' => $learn->getKey()
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
			//
		});
	</script>
@endpush
