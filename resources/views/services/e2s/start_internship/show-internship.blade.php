@extends('services.service')

@section('service')Работодатель. Создать практику@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
			['title' => 'Выбор практики', 'active' => true, 'context' => 'internship', 'link' => route('e2s.start_internship.step2', ['sid' => session()->getId()])],
			['title' => 'Выбор графика практики', 'active' => false, 'context' => 'timetable'],
			['title' => 'Выбор практикантов', 'active' => false, 'context' => null],
			['title' => 'Выбор руководителя практики', 'active' => false, 'context' => 'teacher'],
			['title' => 'Подтверждение выбора', 'active' => false],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">Просмотр практики &laquo;{{ $internship->iname }}&raquo; у работодателя
			&laquo;{{ $internship->employer->name }}&raquo;</h3>
	</div>
	<div class="block-content p-4">
		@php
			$fields = [
				['name' => 'iname', 'title' => 'Наименование практики', 'type' => 'text', 'value' => $internship->iname],
				['name' => 'itype', 'title' => 'Тип практики', 'type' => 'text', 'value' => $internship->itype, 'cast' => function($key) {
    				$options = [
                        'Открытая практика' => 'Открытая практика (практикант может записаться самостоятельно)',
						'Закрытая практика' => 'Закрытая практика (практикантов выбирает работодатель)'
					];
                    return $options[$key];
				}],
				['name' => 'status', 'title' => 'Статус практики', 'type' => 'text', 'value' => $internship->status, 'cast' => function($key) {
    				$options = [
						'Планируется' => 'Планируется (нет практикантов)',
						'Выполняется' => 'Выполняется (есть назначенные практиканты)',
						'Закрыта' => 'Завершена',
					];
                    return $options[$key];
				}],
				['name' => 'program', 'title' => 'Программа практики', 'type' => 'editor', 'value' => $internship->program],
			];
		@endphp

		@foreach($fields as $field)
			<div class="row mb-4">
				<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}</label>
				@switch($field['type'])

					@case('text')
					@case('email')
					@case('number')
					@if(isset($field['cast']))
						@php($value = $field['cast']($field['value']))
					@else
						@php($value = $field['value'])
					@endif
					<div class="col-sm-5">
						<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}"
							   value="{{ $value }}" disabled>
					</div>
					@break

					@case('date')
					<div class="col-sm-5">
						<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
							   name="{{ $field['name'] }}" data-date-format="d.m.Y"
							   value="{{ $field['value'] }}"
							   disabled>
					</div>
					@break

					@case('textarea')
					<div class="col-sm-5">
						<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
								  cols="30"
								  rows="5" disabled>{{ $field['value'] }}</textarea>
					</div>
					@break

					@case('editor')
					<div class="col-sm-9">
						<div class="row">
							<div class="document-editor__toolbar"></div>
						</div>
						<div class="row row-editor">
							<div class="editor" id="{{ $field['name'] }}" name="{{ $field['name'] }}">{!! $field['value'] !!}</div>
						</div>
					</div>
					@break;
				@endswitch
			</div>
		@endforeach
	</div>

	<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
		<div class="row">
			<div class="col-sm-3 col-form-label">&nbsp;</div>
			<div class="col-sm-5">
				<a class="btn btn-primary pl-3"
				   href="{{ route('e2s.start_internship.step2', ['sid' => session()->getId()]) }}"
				   role="button">Закрыть</a>
			</div>
		</div>
	</div>
@endsection

@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/ckeditor.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/ckeditor.js') }}"></script>
	<script>
		DecoupledDocumentEditor
			.create(document.querySelector('.editor'), {
				toolbar: {
					items: [
						'heading',
						'|',
						'fontSize',
						'fontFamily',
						'|',
						'fontColor',
						'fontBackgroundColor',
						'|',
						'bold',
						'italic',
						'underline',
						'strikethrough',
						'subscript',
						'superscript',
						'highlight',
						'|',
						'alignment',
						'|',
						'numberedList',
						'bulletedList',
						'|',
						'outdent',
						'indent',
						'codeBlock',
						'|',
						'todoList',
						'link',
						'blockQuote',
						'insertTable',
						'|',
						'undo',
						'redo'
					]
				},
				language: 'ru',
				table: {
					contentToolbar: [
						'tableColumn',
						'tableRow',
						'mergeTableCells',
						'tableCellProperties',
						'tableProperties'
					]
				},
				licenseKey: '',
			})
			.then(editor => {
				window.editor = editor;

				document.querySelector('.document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
				document.querySelector('.ck-toolbar').classList.add('ck-reset_all');

				editor.isReadOnly = true;
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});
	</script>
@endpush
