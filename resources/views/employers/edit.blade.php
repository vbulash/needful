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
			@if($show)
				Просмотр
			@else
				Редактирование
			@endif
			анкеты работодателя &laquo;{{ $employer->name }}&raquo;
			@if(!$show)
				<br/>
				<span class="required">*</span> - поля, обязательные для заполнения</p>
			@endif
		</h3>
	</div>
	<form role="form" class="p-5" method="post"
		  id="employer-edit" name="employer-edit"
		  action="{{ route('employers.update', ['employer' => $employer->getKey(), 'sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@method('PUT')

		<div class="block-content p-4">
			@include('employers.assign')
			@php
				$fields = [
					['name' => 'name', 'title' => 'Наименование организации', 'required' => true, 'type' => 'text', 'value' => $employer->name],
					['name' => 'contact', 'title' => 'Контактное лицо', 'required' => false, 'type' => 'text', 'value' => $employer->contact],
					['name' => 'address', 'title' => 'Фактический адрес', 'required' => false, 'type' => 'text', 'value' => $employer->address],
					['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $employer->phone],
					['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'text', 'value' => $employer->email],
					['name' => 'inn', 'title' => 'Индивидуальный номер налогоплательщика (ИНН)', 'required' => true, 'type' => 'text', 'value' => $employer->inn],
					['name' => 'kpp', 'title' => 'КПП', 'required' => false, 'type' => 'text', 'value' => $employer->kpp],
					['name' => 'ogrn', 'title' => 'ОГРН / ОГРНИП', 'required' => false, 'type' => 'text', 'value' => $employer->ogrn],
					['name' => 'official_address', 'title' => 'Юридический адрес', 'required' => false, 'type' => 'text', 'value' => $employer->official_address],
					['name' => 'post_address', 'title' => 'Почтовый адрес', 'required' => true, 'type' => 'text', 'value' => $employer->post_address],
					['name' => 'description', 'title' => 'Краткое описание организации (основная деятельность)', 'required' => false, 'type' => 'textarea', 'value' => $employer->description],
					['name' => 'expectation', 'title' => 'Какие результаты ожидаются от практикантов / выпускников?', 'required' => false, 'type' => 'textarea', 'value' => $employer->expectation],
				];
			@endphp

			@foreach($fields as $field)
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">
						{{ $field['title'] }} @if($field['required'] && !$show)
							<span class="required">*</span>
						@endif
					</label>
					<div class="col-sm-5">
						@switch($field['type'])

							@case('text')
							@case('email')
							@case('number')
							<input type="{{ $field['type'] }}" class="form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}"
								   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
								   @if($show) disabled @endif
							>
							@break

							@case('date')
							<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" data-date-format="d.m.Y"
								   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
								   @if($show) disabled @endif
							>
							@break

							@case('textarea')
							<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
									  cols="30"
									  rows="5"
									  @if($show) disabled @endif
							>
								{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}
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
					@if($show)
						<a class="btn btn-primary pl-3"
						   href="{{ route('employers.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@else
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="{{ route('employers.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@endif
				</div>
			</div>
		</div>
	</form>
@endsection
