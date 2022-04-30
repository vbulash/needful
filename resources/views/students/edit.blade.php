@extends('services.service')

@section('service')Работа с практикантами @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Практиканты', 'active' => true, 'context' => 'student', 'link' => route('students.index', ['sid' => session()->getId()])],
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
			анкеты практиканта &laquo;{{ $student->getTitle() }}&raquo;
			@if(!$show)
				<br/>
				<small><span class="required">*</span> - поля, обязательные для заполнения</small>
			@endif
		</h3>
	</div>
	<form role="form" method="post"
		  id="student-edit" name="student-edit"
		  action="{{ route('students.update', ['student' => $student->getKey(), 'sid' => session()->getId()]) }}"
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@method('PUT')

		<div class="block-content p-4">
			@include('students.assign')
			@php
				$view = $show ?? false;
				$fields = [
					['name' => 'lastname', 'title' => 'Фамилия', 'required' => true, 'type' => 'text', 'value' => $student->lastname],
					['name' => 'firstname', 'title' => 'Имя', 'required' => true, 'type' => 'text', 'value' => $student->firstname],
					['name' => 'surname', 'title' => 'Отчество', 'required' => false, 'type' => 'text', 'value' => $student->surname],
					['name' => 'sex', 'title' => 'Пол', 'required' => true, 'type' => 'select', 'options' => [
						'Мужской' => 'Мужской',
						'Женский' => 'Женский',
					], 'value' => $student->sex],
					['name' => 'birthdate', 'title' => 'Дата рождения', 'required' => true, 'type' => 'date', 'value' => $student->birthdate->format('d.m.Y')],
					['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text', 'value' => $student->phone],
					['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $student->email],
					['name' => 'parents', 'title' => 'ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать', 'required' => false, 'type' => 'textarea', 'value' => $student->parents],
					['name' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов', 'required' => false, 'type' => 'textarea', 'value' => $student->parentscontact],
					['name' => 'passport', 'title' => 'Данные паспорта (серия, номер, кем и когда выдан)', 'required' => false, 'type' => 'textarea', 'value' => $student->passport],
					['name' => 'address', 'title' => 'Адрес проживания', 'required' => false, 'type' => 'textarea', 'value' => $student->address],
					['name' => 'institutions', 'title' => 'Учебное заведение (на момент заполнения)', 'required' => false, 'type' => 'textarea', 'value' => $student->institutions],
					['name' => 'grade', 'title' => 'Класс / группа (на момент заполнения)', 'required' => false, 'type' => 'text', 'value' => $student->grade],
					['name' => 'hobby', 'title' => 'Увлечения (хобби)', 'required' => false, 'type' => 'textarea', 'value' => $student->hobby],
					['name' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?', 'required' => false, 'type' => 'number', 'value' => $student->contestachievements],
					['name' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения', 'required' => false, 'type' => 'textarea', 'value' => $student->dream],
					['name' => 'dream', 'title' => 'Чем хочется заниматься в жизни?', 'required' => false, 'type' => 'textarea', 'value' => $student->dream],
				];
			@endphp

			@foreach($fields as $field)
				@switch($field['type'])
					@case('hidden')
					@break

					@default
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
							@if($field['required'] && !$view)
								<span class="required">*</span>
							@endif</label>
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
									   @if($view) disabled @endif
								>
							</div>
							@break

							@case('textarea')
							<div class="col-sm-5">
						<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
								  cols="30"
								  rows="5"
								  @if($view) disabled @endif
						>{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}</textarea>
							</div>
							@break

							@case('date')
							<div class="col-sm-5">
								<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}" data-date-format="d.m.Y"
									   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
									   @if($view) disabled @endif
								>
							</div>
							@break

							@case('select')
							<div class="col-sm-5">
								<select class="form-control select2" name="{{ $field['name'] }}"
										id="{{ $field['name'] }}" @if($view) disabled @endif>
									@foreach($field['options'] as $key => $value)
										<option value="{{ $key }}"
												@if(isset($field['value']))
													@if($field['value'] == $key) selected @endif>
											@endif
											{{ $value }}</option>
									@endforeach
								</select>
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
					@if($show)
						<a class="btn btn-primary pl-3"
						   href="{{ route('students.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@else
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="{{ route('students.index', ['sid' => session()->getId()]) }}"
						   role="button">Закрыть</a>
					@endif
				</div>
			</div>
		</div>
	</form>
@endsection
