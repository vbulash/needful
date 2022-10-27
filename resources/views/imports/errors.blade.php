@extends('layouts.chain')

@section('steps')
	@php
		$steps = [['title' => 'Импорт учащихся', 'active' => true, 'context' => 'null']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			{{ $title }}
		</div>
	</div>

	<div class="block-content p-4">
		<div>
			@foreach ($messages as $message)
				{!! $message !!}<br />
			@endforeach
		</div>
	</div>

	<div class="block-content block-content-full block-content-sm bg-body-light">
		<a href="{{ route('import.index') }}" class="btn btn-primary">&lt; Импорт</a>
	</div>
@endsection
