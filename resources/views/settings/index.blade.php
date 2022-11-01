@extends('layouts.blocks')

@section('header')
	Настройки платформы &laquo;{{ env('APP_NAME') }}&raquo;
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Уведомления', 'active' => true, 'context' => null]];
	@endphp
@endsection

@section('buttons')
	{{-- <div class="col-sm-3 col-form-label">&nbsp;</div> --}}
	<div class="col-sm-5">
		<form action="{{ route('settings.notifications.store') }}" method="post" enctype="multipart/form-data" id="states-form">
			@csrf
			<input type="hidden" value="" name="states" id="states">
			<button href="" type="submit" class="btn btn-primary">Сохранить</button>
		</form>
	</div>
@endsection

@php
	$index = 1;
	if (isset($nstates))
		$states = json_decode($nstates);
@endphp
@section('blocks')
	<div class="col-12">
		<div class="block block-rounded">
			<div class="block-header block-header-default mb-4">
				<p>Выключение флага отключает рассылку соответствующего уведомления</p>
			</div>
			<div class="block-content p-4 d-flex flex-wrap">
				@foreach (config('settings.notifications') as $group)
					<div class="col-sm-4">
						<div class="block block-rounded">
							<div class="block-header block-header-default">
								<p>{{ $group['group'] }}</p>
							</div>
							<div class="block-content p-4">
								@foreach ($group['classes'] as $key => $value)
									<div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" name="check-{{ $index }}" id="check-{{ $index }}"
											data-class="{{ str_replace('\\', '.', $key) }}"
											@if (isset($nstates))
												@if (in_array(str_replace('\\', '.', $key), $states))
													checked
												@endif
											@else
												checked
											@endif
										>
										<label class="form-check-label" for="check-{{ $index++ }}">{{ $value }}</label>
									</div>
								@endforeach
							</div>
						</div>
					</div>
				@endforeach
			</div>
			<div class="block-content block-content-full block-content-sm bg-body-light fs-sm">
				<div class="row">
					@yield('buttons')
				</div>
			</div>
		</div>
	</div>
@endsection

@push('js_after')
	<script>
		let states = [];

		document.querySelectorAll('.form-check-input').forEach((checkbox) => {
			checkbox.addEventListener('change', (event) => {
				const key = event.target.dataset.class;
				if (event.target.checked) {
					if (states.indexOf(key) == -1)
						states.push(key);
				} else {
					states.splice(states.indexOf(key), 1);
				}
			}, false);
		});

		document.getElementById('states-form').addEventListener('submit', () => {
			document.getElementById('states').value = JSON.stringify(states);
		}, false);

		document.addEventListener('DOMContentLoaded', () => {
			@if (isset($nstates))
				states = JSON.parse('{!! $nstates !!}');
			@else
				@foreach (config('settings.notifications') as $group)
					@foreach ($group['classes'] as $key => $value)
						states.push("{{ str_replace('\\', '.', $key) }}");
					@endforeach
				@endforeach
			@endif
		}, false);
	</script>
@endpush
