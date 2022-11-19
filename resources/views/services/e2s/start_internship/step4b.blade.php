@extends('services.service')

@section('service')
	Работодатель. Создать практику
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1')], ['title' => 'Выбор практики', 'active' => false, 'context' => 'internship', 'link' => route('e2s.start_internship.step2')], ['title' => 'Выбор графика практики', 'active' => false, 'context' => 'timetable', 'link' => route('e2s.start_internship.step3')], ['title' => 'Выбор практикантов', 'active' => false, 'context' => null, 'link' => route('e2s.start_internship.step4')], ['title' => 'Выбор руководителя практики', 'active' => true, 'context' => 'teacher'], ['title' => 'Подтверждение выбора', 'active' => false]];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title">Выбор руководителя практики</h3>
	</div>
	<form role="form" method="post" action="{{ route('e2s.start_internship.step4b.select') }}" autocomplete="off"
		enctype="multipart/form-data">
		@csrf
		<div class="block-content p-4">
			<div class="row mb-4">
				<label class="col-sm-3 col-form-label" for="teacher">
					Руководитель практики <span class="required">*</span>
				</label>
				<div class="col-sm-5">
					<select class="form-control select2" name="teacher" id="teacher">
						@foreach ($teachers as $teacher)
							<option value="{{ $teacher->getKey() }}" @if ($loop->first) selected @endif>{{ $teacher->getTitle() }}</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>
		<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
			<div class="row">
				<div class="col-sm-3 col-form-label">&nbsp;</div>
				<div class="col-sm-5">
					<button type="submit" class="btn btn-primary">Сохранить</button>
				</div>
			</div>
		</div>
	</form>
@endsection
