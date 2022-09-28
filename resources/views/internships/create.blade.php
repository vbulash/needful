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
	Новая стажировка
@endsection

@section('form.params')
	id="{{ form(\App\Models\Internship::class, $mode, 'id') }}" name="{{ form(\App\Models\Internship::class, $mode, 'name') }}"
	action="{{ form(\App\Models\Internship::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'iname', 'title' => 'Название стажировки', 'required' => true, 'type' => 'text'],
			['name' => 'itype', 'title' => 'Тип стажировки', 'required' => true, 'type' => 'select', 'options' => [
				'Открытая стажировка' => 'Открытая стажировка (практикант может записаться самостоятельно)',
				'Закрытая стажировка' => 'Закрытая стажировка (практикантов выбирает работодатель)'
			]],
			['name' => 'status', 'title' => 'Статус стажировки', 'required' => false, 'type' => 'text', 'disabled' => true, 'value' => 'Планируется'],
			['name' => 'short', 'title' => 'Краткая программа (для писем и сообщений)', 'type' => 'textarea', 'required' => false],
			['name' => 'program', 'title' => 'Программа стажировки', 'type' => 'editor', 'required' => true],
			['name' => 'employer_id', 'type' => 'hidden', 'value' => $employer->getKey()],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(\App\Models\Internship::class, $mode, 'close') }}
@endsection

@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/ckeditor.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/ckeditor.js') }}"></script>
	<script type="module">
		import {SimpleUploadAdapter} from '@ckeditor/ckeditor5-upload';

		DecoupledDocumentEditor
			.create(document.querySelector('.editor'), {
				plugins: [SimpleUploadAdapter],
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
						'|',
						'alignment',
						'|',
						'numberedList',
						'bulletedList',
						'|',
						'outdent',
						'indent',
						'|',
						'link',
						'blockQuote',
						'imageUpload',
						'insertTable',
						'mediaEmbed',
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

				//editor.isReadOnly = true;
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});

		document.getElementById('{{ form(\App\Models\Internship::class, $mode, 'name') }}').addEventListener('submit', () => {
			document.getElementById('program').value = editor.getData();
		}, false);
	</script>
@endpush

@push('js_after')
	<script src="{{ asset('js/ckeditor.js') }}"></script>
	<script>
		let formName = "{ form(\App\Models\Internship::class, $mode, 'name') }}";
		let ckefield = 'program';

		DecoupledDocumentEditor
			.create(document.querySelector('.editor'))
			.then(editor => {
				window.editor = editor;

				document.querySelector('.document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
				document.querySelector('.ck-toolbar').classList.add('ck-reset_all');
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: 7qp6pd211rg0-qmvmsysb38gy');
				console.error(error);
			});

		document.getElementById(formName).addEventListener('submit', () => {
			document.getElementById(ckefield).value = editor.getData();
		}, false);
	</script>
@endpush
