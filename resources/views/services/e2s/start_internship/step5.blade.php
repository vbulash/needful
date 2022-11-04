@extends('services.service')

@section('service')Работодатель. Создать стажировку@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1')],
			['title' => 'Выбор стажировки', 'active' => false, 'context' => 'internship', 'link' => route('e2s.start_internship.step2')],
			['title' => 'Выбор графика стажировки', 'active' => false, 'context' => 'timetable', 'link' => route('e2s.start_internship.step3')],
			['title' => 'Выбор практикантов', 'active' => false, 'context' => null, 'link' => route('e2s.start_internship.step4')],
			['title' => 'Выбор руководителя практики', 'active' => false, 'context' => 'teacher', 'link' => route('e2s.start_internship.step4b')],
			['title' => 'Подтверждение выбора', 'active' => true],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			Подтверждение параметров новой стажировки<br/>
			<small>Выбраны следующие параметры прохождения стажировки:</small>
		</h3>
	</div>
	<div class="block-content p-4">
		@php
			$context = session('context');
			$fields = [
				['name' => 'employer', 'title' => 'Работодатель'],
				['name' => 'internship', 'title' => 'Стажировка'],
				['name' => 'timetable', 'title' => 'График стажировки'],
				['name' => 'teacher', 'title' => 'Руководитель стажировки'],
				['name' => 'names', 'title' => 'Выбранные, но пока не подтверждённые практиканты'],
			];
		@endphp

		@foreach($fields as $field)
			<div class="row mb-4">
				<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}</label>
				<div class="col-sm-5">
					@if ($field['name'] == 'names')
						<textarea name="{{ $field['name'] }}" id="{{ $field['name'] }}" rows="10"
								  class="form-control col-12" disabled>{{ $context['names'] }}</textarea>
					@else
						<input type="text" class="form-control" id="{{ $field['name'] }}" name="{{ $field['name'] }}"
						   value="{{ $context[$field['name']]->getTitle() }}" disabled>
					@endif
				</div>
			</div>
		@endforeach
		<input type="hidden" name="ids" value="{{ $ids }}">

		<p>Запланировать стажировку практикантов?</p>
		<p>
			Нажатие &laquo;Да&raquo; зарегистрирует стажировку практикантов.<br/>
			Нажатие &laquo;Нет&raquo; вернет вас на главную страницу сайта для выбора услуг
		</p>
	</div>

	<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
		<div class="row">
			<div class="col-sm-3 col-form-label">&nbsp;</div>
			<div class="col-sm-5">
				<a class="btn btn-primary pl-3"
				   href="{{ route('e2s.start_internship.step5.create', ['ids' => $ids, 'sid' => session()->getId()]) }}"
				   role="button">Да</a>
				<a class="btn btn-secondary pl-3"
				   href="{{ route('dashboard', ['sid' => session()->getId()]) }}"
				   role="button">Нет</a>
			</div>
		</div>
	</div>
@endsection
