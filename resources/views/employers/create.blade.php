@extends('services.service')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => true, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship'],
			['title' => 'График стажировки', 'active' => false, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			Новый работодатель<br/>
			<span class="required">*</span> - поля, обязательные для заполнения</p>
		</h3>
	</div>
	<form role="form" class="p-5" method="post"
		  id="employer-create" name="employer-create"
		  action="{{ route('employers.store', ['sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf

		<div class="block-content p-4">
			@include('employers.assign')
			@php
				$fields = [
					['name' => 'name', 'title' => 'Наименование организации', 'required' => true, 'type' => 'text'],
					['name' => 'contact', 'title' => 'Контактное лицо', 'required' => false, 'type' => 'text'],
					['name' => 'address', 'title' => 'Фактический адрес', 'required' => false, 'type' => 'text'],
					['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text'],
					['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'text'],
					['name' => 'inn', 'title' => 'Индивидуальный номер налогоплательщика (ИНН)', 'required' => true, 'type' => 'text'],
					['name' => 'kpp', 'title' => 'КПП', 'required' => false, 'type' => 'text'],
					['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП', 'required' => false, 'type' => 'text'],
					['name' => 'official_address', 'title' => 'Юридический адрес', 'required' => false, 'type' => 'text'],
					['name' => 'post_address', 'title' => 'Почтовый адрес', 'required' => true, 'type' => 'text'],
					['name' => 'description', 'title' => 'Краткое описание организации (основная деятельность)', 'required' => false, 'type' => 'textarea'],
					['name' => 'expectation', 'title' => 'Какие результаты ожидаются от практикантов / выпускников?', 'required' => false, 'type' => 'textarea'],
				];
			@endphp

			@foreach($fields as $field)
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">
						{{ $field['title'] }} @if($field['required']) <span class="required">*</span> @endif
					</label>
					<div class="col-sm-5">
						@switch($field['type'])

							@case('text')
							@case('email')
							@case('number')
							<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}"
								   value="{{ old($field['name']) }}">
							@break

							@case('date')
							<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" data-date-format="d.m.Y"
								   value="{{ old($field['name']) }}">
							@break

							@case('textarea')
							<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
									  cols="30"
									  rows="5">
								{{ old($field['name']) }}
							</textarea>
							@break
						@endswitch
					</div>
				</div>
			@endforeach
		</div>

		<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
			<div class="row">
				<div class="col-sm-3 col-form-label">&nbsp;</div>
				<div class="col-sm-5">
					<button type="submit" class="btn btn-primary">Сохранить</button>
					<a class="btn btn-secondary pl-3"
					   href="{{ route('employers.index', ['sid' => session()->getId()]) }}"
					   role="button">Закрыть</a>
				</div>
			</div>
		</div>
	</form>
@endsection
