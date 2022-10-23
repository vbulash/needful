@extends('layouts.backend')

@section('content')
	@php
		$cards = [
    		[
    			['role' => \App\Http\Controllers\Auth\RoleName::EMPLOYER->value, 'title' => 'Работодатель', 'subtitle' => 'Создать стажировку', 'active' => true, 'icon' => 'fa fa-2x fa-business-time', 'link' => route('e2s.start_internship.step1')],
    			['role' => \App\Http\Controllers\Auth\RoleName::EMPLOYER->value, 'title' => 'Работодатель', 'subtitle' => 'Отслеживать стажировку', 'active' => false, 'icon' => 'fa fa-2x fa-business-time'],
    			['role' => \App\Http\Controllers\Auth\RoleName::EMPLOYER->value, 'title' => 'Работодатель', 'subtitle' => 'Завершить стажировку', 'active' => false, 'icon' => 'fa fa-2x fa-business-time'],
    		],
    		[
    			['role' => \App\Http\Controllers\Auth\RoleName::TRAINEE->value, 'title' => 'Практикант', 'subtitle' => 'Пройти стажировку', 'active' => false, 'icon' => 'fas fa-2x fa-user-graduate'],
    		]
		];
	@endphp
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Платформа &laquo;{{ env('APP_NAME') }}&raquo; позволяет оказывать следующие услуги</h1>
			</div>
{{--			<div class="row items-push">Дополнительный блок</div>--}}
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-12">
				<div class="block block-rounded">
{{--					<div class="block-header block-header-default">--}}
{{--						<h3 class="block-title">Title <small>Subtitle</small></h3>--}}
{{--					</div>--}}
					<div class="block-content">
						@foreach($cards as $row)
							<div class="row items-push">
								@foreach($row as $card)
									@php
										$allowed = false;
										if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)) {
											$allowed = true;
										} elseif (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::EMPLOYER->value) &&
											$card['role'] == \App\Http\Controllers\Auth\RoleName::EMPLOYER->value) {
											$allowed = true;
										} elseif (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value) &&
											$card['role'] == \App\Http\Controllers\Auth\RoleName::TRAINEE->value) {
											$allowed = true;
										}
									@endphp

									@if (!$allowed)
										@continue
									@endif
									@if (!env('SHOW_INACTIVE_TILES') && !$card['active'])
										@continue
									@endif

									<x-tile
										title="{{ $card['title'] }}"
										subtitle="{{ $card['subtitle'] }}"
										active="{{ $card['active'] }}"
										icon="{{ $card['icon'] }}"
										link="{{ $card['link'] ?? '' }}">
									</x-tile>
								@endforeach
							</div>
						@endforeach
					</div>
				</div>
			</div>


		</div>
	</div>
@endsection
