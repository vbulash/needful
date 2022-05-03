@extends('services.service')

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold">
			@yield('interior.header')
			@if($mode != config('global.show'))
				<br/>
				<small><span class="required">*</span> - поля, обязательные для заполнения</small>
			@endif
		</h3>
	</div>
	<form role="form" method="post"
		  @yield('form.params')
		  autocomplete="off" enctype="multipart/form-data">
		@csrf
		@if($mode == config('global.edit'))
			@method('PUT')
		@endif

		<div class="block-content p-4">
			@yield('form.fields')

			@foreach($fields as $field)
				@switch($field['type'])
					@case('hidden')
					@break

					@default
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="{{ $field['name'] }}">{{ $field['title'] }}
							@if($field['required'] && $mode != config('global.show'))
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
									   autocomplete="off"
									   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
									   @if($mode == config('global.show') || isset($field['disabled'])) disabled @endif
								>
							</div>
							@break

							@case('password')
							<div class="col-sm-5">
								<input type="text" class="form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}"
									   autocomplete="new-password"
									   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
									   @if($mode == config('global.show') || isset($field['disabled'])) disabled @endif
								>
							</div>
							@if(isset($field['generate']))
								<div class="col-sm-3">
									<button type="button" name="get-password"
											id="get-password" class="btn btn-primary mb-3">
										Сгенерировать пароль
									</button>
								</div>
							@endif
							@break

							@case('textarea')
							<div class="col-sm-5">
						<textarea class="form-control" name="{{ $field['name'] }}" id="{{ $field['name'] }}"
								  cols="30"
								  rows="5"
								  @if($mode == config('global.show')) disabled @endif
						>{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}</textarea>
							</div>
							@break

							@case('date')
							<div class="col-sm-5">
								<input type="text" class="flatpickr-input form-control" id="{{ $field['name'] }}"
									   name="{{ $field['name'] }}" data-date-format="d.m.Y"
									   value="{{ isset($field['value']) ? old($field['name'], $field['value']) : old($field['name']) }}"
									   @if($mode == config('global.show')) disabled @endif
								>
							</div>
							@break

							@case('select')
							<div class="col-sm-5">
								<select class="form-control select2" name="{{ $field['name'] }}"
										id="{{ $field['name'] }}" @if($mode == config('global.show')) disabled @endif
								>
									@foreach($field['options'] as $key => $value)
										<option value="{{ $key }}"
												@if(isset($field['value']))
													@if($field['value'] == $key)
														selected
											@endif
											@endif
										>
											{{ $value }}</option>
									@endforeach
								</select>
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
										 name="{{ $field['name'] }}_editor">{!! $field['value'] ?? '' !!}</div>
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
					@if($mode == config('global.show'))
						<a class="btn btn-primary pl-3"
						   href="@yield('form.close')"
						   role="button">Закрыть</a>
					@else
						<button type="submit" class="btn btn-primary">Сохранить</button>
						<a class="btn btn-secondary pl-3"
						   href="@yield('form.close')"
						   role="button">Закрыть</a>
					@endif
				</div>
			</div>
		</div>
	</form>
@endsection
