@extends('contracts.steps.wizard')

@section('service')
	Регистрация договора на практику
@endsection

@section('form.fields')
	@php
		$fields = [];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title">
			Выберите образовательное учреждение для регистрации договора на практику
		</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="schools_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Тип образовательного учреждения</th>
							<th>Краткое название</th>
							<th>Телефон</th>
							<th>Электронная почта</th>
							<th>Действия</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Активных образовательных учреждений пока нет...</p>
		@endif
	</div>
@endsection

@if ($count > 0)
	@push('css_after')
		<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
	@endpush

	@push('js_after')
		<script src="{{ asset('js/datatables.js') }}"></script>
		<script>
			$(function() {
				window.datatable = $('#schools_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('contracts.steps.index.data') !!}',
					responsive: true,
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'type',
							name: 'type',
							responsivePriority: 2
						},
						{
							data: 'short',
							name: 'short',
							responsivePriority: 1
						},
						{
							data: 'phone',
							name: 'phone',
							responsivePriority: 2
						},
						{
							data: 'email',
							name: 'email',
							responsivePriority: 2
						},
						{
							data: 'action',
							name: 'action',
							sortable: false,
							responsivePriority: 1,
							className: 'no-wrap dt-actions'
						}
					]
				});
			});
		</script>
	@endpush
@endif
