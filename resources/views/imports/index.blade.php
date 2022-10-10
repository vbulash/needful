@extends('layouts.backend')

@section('content')
	@php
		$cards = [
    		[
    			['title' => 'Пользователи', 'active' => true, 'icon' => 'fa-2x fa-solid fa-users', 'link' => route('dashboard')],
    		],
		];
	@endphp
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Платформа &laquo;{{ env('APP_NAME') }}&raquo; позволяет импортировать следующие объекты</h1>
			</div>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-12">
				<div class="block block-rounded">
					<div class="block-content">
						@foreach($cards as $row)
							<div class="row items-push">
								@foreach($row as $card)
									@if (!env('SHOW_INACTIVE_TILES') && !$card['active'])
										@continue
									@endif

									<x-tile
										title="{{ $card['title'] }}"
										subtitle=""
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
