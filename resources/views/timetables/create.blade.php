@extends('services.service')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => 'График стажировки', 'active' => true, 'context' => 'timetable', 'link' => route('timetables.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			Новый график стажировки<br/>
			<span class="required">*</span> - поля, обязательные для заполнения
		</h3>
	</div>
	<form role="form" method="post"
		  id="timetable-create" name="timetable-create"
		  action="{{ route('timetables.store', ['sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf

		<div class="block-content p-4">
			@php
				$fields = [
					['name' => 'start', 'title' => 'Начало', 'required' => true, 'type' => 'date', 'placeholder' => 'Выберите дату'],
					['name' => 'end', 'title' => 'Завершение', 'required' => true, 'type' => 'date', 'placeholder' => 'Выберите дату'],
					['name' => 'name', 'title' => 'Наименование записи графика стажировки', 'required' => false, 'type' => 'text'],
					['name' => 'internship_id', 'type' => 'hidden', 'value' => $internship->getKey()],
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
							@if($field['required'])
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
								>
							</div>
							@break

							@case('date')
							<div class="col-sm-5">
								<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}" data-date-format="d.m.Y"
									   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
									   @if(isset($field['placeholder']))
										   placeholder="{{ $field['placeholder'] }}"
									   @endif
								>
							</div>
							@break

							@case('select')
							<div class="col-sm-5">
								<select class="form-control select2" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
								>
									@foreach($field['options'] as $key => $option)
										<option value="{{ $key }}"
												@if($key == $field['value']) selected @endif>{{ $option }}</option>
									@endforeach
								</select>
							</div>
							@break

							@case('textarea')
							<div class="col-sm-5">
								<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
										  cols="30"
										  rows="5"
								>
									{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}
								</textarea>
							</div>
							@break

							@case('hidden')
							<input type="{{ $field['type'] }}" id="{{ $field['name'] }}"
								   name="{{ $field['name'] }}" value="{{ $field['value'] }}">
							@break
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
					<button type="submit" class="btn btn-primary">Сохранить</button>
					<a class="btn btn-secondary pl-3"
					   href="{{ route('timetables.index', ['sid' => session()->getId()]) }}"
					   role="button">Закрыть</a>
				</div>
			</div>
		</div>
	</form>
@endsection
