@extends('layouts.detail')

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => true, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => 'График стажировки или Специальности для стажировки', 'active' => false, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif стажировки &laquo;{{ $internship->iname }}&raquo;
@endsection

@section('form.params')
	id="{{ form($internship, $mode, 'id') }}" name="{{ form($internship, $mode, 'name') }}"
	action="{{ form($internship, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'iname', 'title' => 'Название стажировки', 'required' => true, 'type' => 'text', 'value' => $internship->iname],
			['name' => 'itype', 'title' => 'Тип стажировки', 'required' => true, 'type' => 'select', 'options' => [
				'Открытая стажировка' => 'Открытая стажировка (практикант может записаться самостоятельно)',
				'Закрытая стажировка' => 'Закрытая стажировка (практикантов выбирает работодатель)'
			], 'value' => $internship->itype],
			['name' => 'status', 'title' => 'Статус стажировки', 'required' => false, 'type' => 'select', 'options' => [
				'Планируется' => 'Планируется (нет практикантов)',
				'Выполняется' => 'Выполняется (есть назначенные практиканты)',
				'Закрыта' => 'Завершена',
			], 'value' => $internship->status],
			['name' => 'program', 'title' => 'Программа стажировки', 'type' => 'editor', 'required' => true, 'value' => $internship->program],
			['name' => 'employer_id', 'type' => 'hidden', 'value' => $internship->employer->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($internship, $mode, 'close') }}
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

				@if($mode == config('global.show')) editor.isReadOnly = true; @endif
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});

		document.getElementById('internship-create').addEventListener('submit', () => {
			document.getElementById('program').value = editor.getData();
		}, false);
	</script>
@endpush
