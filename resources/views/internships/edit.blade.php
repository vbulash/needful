@extends('services.service')

@section('service')
	Работа с работодателями
@endsection

@section('body-params')
	data-editor="DecoupledDocumentEditor" data-collaboration="false"
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => true, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => 'График стажировки', 'active' => false, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			@if($show)
				Просмотр
			@else
				Редактирование
			@endif стажировки &laquo;{{ $internship->iname }}&raquo;
			@if(!$show)
				<br/>
				<span class="required">*</span> - поля, обязательные для заполнения
			@endif
		</h3>
	</div>
	<form role="form" method="post"
		  id="internship-edit" name="internship-edit"
		  action="{{ route('internships.update', ['internship' => $internship->getKey(), 'sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@method('PUT')

		<div class="block-content p-4">
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

			@foreach($fields as $field)
				@switch($field['type'])
					@case('hidden')
					@break

					@default
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">
							{{ $field['title'] }}
							@if($field['required'] && !$show)
								<span class="required">*</span>
							@endif
						</label>
						@break
						@endswitch

						@switch($field['type'])

							@case('text')
							@case('email')
							@case('number')
							<div class="col-sm-5">
								<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}"
									   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
									   @if($show) disabled @endif
								>
							</div>
							@break

							@case('date')
							<div class="col-sm-5">
								<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}" data-date-format="d.m.Y"
									   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
									   @if($show) disabled @endif
								>
							</div>
							@break

							@case('select')
							<div class="col-sm-5">
								<select class="form-control select2" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
										@if($show) disabled @endif
								>
									@foreach($field['options'] as $key => $option)
										<option value="{{ $key }}"
												@if($key == $field['value']) selected @endif
										>{{ $option }}</option>
									@endforeach
								</select>
							</div>
							@break

							@case('textarea')
							<div class="col-sm-5">
								<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
										  cols="30"
										  rows="5"
										  @if($show) disabled @endif
								>
									{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}
								</textarea>
							</div>
							@break

							@case('hidden')
							<input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" value="{{ $field['value'] }}">
							@break

							@case('editor')
							<input type="hidden" id="{{ $field['name'] }}" name="{{ $field['name'] }}">
							<div class="col-sm-9">
								<div class="row">
									<div class="document-editor__toolbar"></div>
								</div>
								<div class="row row-editor">
									<div class="editor" id="{{ $field['name'] }}_editor"
										 name="{{ $field['name'] }}_editor">{!! $field['value'] !!}</div>
								</div>
							</div>
							@break;
						@endswitch
						@switch($field['type'])
							@case('hidden')
							@break

							@default
					</div>
					@break
				@endswitch
			@endforeach
		</div>

		<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
			<div class="row">
				<div class="col-sm-3 col-form-label">&nbsp;</div>
				<div class="col-sm-5">
					@if($show)
						<a class="btn btn-primary pl-3"
						   href="{{ route('internships.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@else
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="{{ route('internships.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@endif
				</div>
			</div>
		</div>
	</form>
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

				@if($show) editor.isReadOnly = true; @endif
			})
			.catch(error => {
				console.error('Oops, something went wrong!');
				console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
				console.warn('Build id: bfknlbbh0ej1-27rpc1i5joqr');
				console.error(error);
			});

		document.getElementById('internship-edit').addEventListener('submit', () => {
			document.getElementById('program').value = editor.getData();
		}, false);
	</script>
@endpush
