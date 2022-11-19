@extends('layouts.detail')

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('service')
	Работа с работодателями
	@if (isset(session('context')['chain']))
		(только цепочка значений)
	@endif
@endsection

@section('steps')
	@php
		if (isset(session('context')['chain']))
			$title = 'График практики';
		else
			$title = 'График практики или Специальности для практики';
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Практика', 'active' => true, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => $title, 'active' => false, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif практики &laquo;{{ $internship->iname }}&raquo;
@endsection

@section('form.params')
	id="{{ form($internship, $mode, 'id') }}" name="{{ form($internship, $mode, 'name') }}"
	action="{{ form($internship, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'iname', 'title' => 'Название практики', 'required' => true, 'type' => 'text', 'value' => $internship->iname],
			// ['name' => 'itype', 'title' => 'Тип практики', 'required' => true, 'type' => 'select', 'options' => [
			// 	'Открытая практика' => 'Открытая практика (практикант может записаться самостоятельно)',
			// 	'Закрытая практика' => 'Закрытая практика (практикантов выбирает работодатель)'
			// ], 'value' => $internship->itype],
			['name' => 'itype', 'type' => 'hidden', 'value' => $internship->itype],
			['name' => 'status', 'title' => 'Статус практики', 'required' => false, 'type' => 'select', 'options' => [
				'Планируется' => 'Планируется',
				'Выполняется' => 'Выполняется',
				'Закрыта' => 'Завершена',
			], 'value' => $internship->status],
			['name' => 'short', 'title' => 'Краткая программа (для писем и сообщений)', 'type' => 'textarea', 'required' => false, 'value' => $internship->short],
			['name' => 'program', 'title' => 'Программа практики', 'type' => 'editor', 'required' => true, 'value' => $internship->program],
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
		let readonly = Boolean({{ $mode == config('global.show') ? 'true' : 'false' }});
		let formName = "{{ form($internship, $mode, 'name') }}";
		let ckefield = 'program';

		DecoupledDocumentEditor
			.create(document.querySelector('.editor'))
			.then(editor => {
				window.editor = editor;

				document.querySelector('.document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
				document.querySelector('.ck-toolbar').classList.add('ck-reset_all');

				if (readonly)
					editor.enableReadOnlyMode('internship.lock');
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
