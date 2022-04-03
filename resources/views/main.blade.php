@extends('layouts.backend')

@section('content')
	@php
		$cards = [
    		[
    			['role' => 'Работодатель', 'title' => 'Работодатель', 'subtitle' => 'Начать стажировку практиканта', 'class' => 'bg-gd-sea', 'icon' => 'fa fa-2x fa-business-time', 'link' => 'javascript:void(0)'],
    			['role' => 'Работодатель', 'title' => 'Работодатель', 'subtitle' => 'Отслеживать стажировку', 'class' => 'bg-gd-sea', 'icon' => 'fa fa-2x fa-business-time', 'link' => 'javascript:void(0)'],
    			['role' => 'Работодатель', 'title' => 'Работодатель', 'subtitle' => 'Завершить стажировку', 'class' => 'bg-gd-sea', 'icon' => 'fa fa-2x fa-business-time', 'link' => 'javascript:void(0)'],
    		],
    		[
    			['role' => 'Практикант', 'title' => 'Практикант', 'subtitle' => 'Пройти стажировку', 'class' => 'bg-gd-dusk', 'icon' => 'fas fa-2x fa-user-graduate', 'link' => 'javascript:void(0)'],
    		]
		];
	@endphp
		<div class="block-header block-header-default">
			<p>Вам доступны следующие действия:</p>
		</div>
		@foreach($cards as $row)
			<div class="row items-push">
				@foreach($row as $card)
					@php($allowed = false)
					@hasrole('Администратор')
					@php($allowed = true)
					@endhasrole
					@hasrole('Работодатель')
					@if($card['role'] == 'Работодатель')
						@php($allowed = true)
					@endif
					@endhasrole
					@hasrole('Практикант')
					@if($card['role'] == 'Практикант')
						@php($allowed = true)
					@endif
					@endhasrole

					@if(!$allowed)
						@continue
					@endif

					<div class="col-md-6 col-xl-4 mb-4">
						<a class="block block-rounded block-transparent block-link-pop {{ $card['class'] }} h-100 mb-0"
						   href="{{ $card['link'] }}">
							<div
								class="block-content block-content-full d-flex align-items-center justify-content-between">
								<div>
									<p class="fs-lg fw-semibold mb-0 text-white">{{ $card['title'] }}</p>
									<p class="text-white-75 mb-0">{{ $card['subtitle'] }}</p>
								</div>
								<div class="ms-3 item">
									<i class="{{ $card['icon'] }} text-white-50"></i>
								</div>
							</div>
						</a>
					</div>
				@endforeach
			</div>
		@endforeach
@endsection
