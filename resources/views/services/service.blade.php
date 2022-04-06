@extends('layouts.backend')

@yield('steps')

@section('content')
	@php
		$steps = [
			['title' => 'Выбрать работодателя', 'subtitle' => '', 'done' => true, 'active' => false],
			['title' => 'Выбор стажировки', 'subtitle' => '', 'done' => false, 'active' => true],
			['title' => 'Выбор практиканта', 'subtitle' => '', 'done' => false, 'active' => false],
			['title' => 'Подтверждение выбора', 'subtitle' => '', 'done' => false, 'active' => false],
		];
	@endphp
	<div class="block-header block-header-default">
		<div class="row items-push">
			@foreach($steps as $step)
				@if($loop->first) @php($left = true) @endif
				<div class="col-md-6 col-xl-4 mb-4">
					<a class="block block-rounded block-transparent block-link-pop {!! $left ? "bg-gd-sea" : "" !!} h-100 mb-0"
					   href="javascript:void(0)">
						<div
							class="block-content block-content-full d-flex align-items-center justify-content-between">
							<div>
								<p class="fs-lg fw-semibold mb-0 text-white">{{ $step['title'] }}</p>
								<p class="text-white-75 mb-0">{{ $step['subtitle'] }}</p>
							</div>

							<div class="ms-3 item">
								@if($left)
								<i class="fas fa-check text-white-50"></i>
									@elseif()
							</div>
						</div>
					</a>
				</div>
				@if($step['active'])
					@php($left = false)
				@endif
			@endforeach
		</div>
	</div>

@endsection

