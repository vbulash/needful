@extends('layouts.chain')

@section('service')
	Планирование практикантов по заявкам на практику от образовательных учреждений
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Заявки на практику', 'active' => true, 'context' => 'order'], ['title' => 'Ответы работодателей', 'active' => false, 'context' => 'answer'], ['title' => 'Практиканты', 'active' => false, 'context' => 'answer.students']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold mb-4">Заявки на практику от образовательных учреждений</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="orders_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Учебное заведение</th>
							<th>Название практики</th>
							<th>Дата начала</th>
							<th>Дата окончания</th>
							<th>Место практики</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Заявок на практику пока нет...</p>
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
				window.datatable = $('#orders_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('planning.orders.index.data') !!}',
					responsive: true,
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'school',
							name: 'school',
							responsivePriority: 1
						},
						{
							data: 'name',
							name: 'name',
							responsivePriority: 2
						},
						{
							data: 'start',
							name: 'start',
							responsivePriority: 1
						},
						{
							data: 'end',
							name: 'end',
							responsivePriority: 1
						},
						{
							data: 'place',
							name: 'place',
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

				window.datatable.on('draw', function() {
					$('.dropdown-toggle.actions').on('shown.bs.dropdown', (event) => {
						const menu = event.target.parentElement.querySelector('.dropdown-menu');
						let parent = menu.closest('.dataTables_wrapper');
						const parentRect = parent.getBoundingClientRect();
						parentRect.top = Math.abs(parentRect.top);
						const menuRect = menu.getBoundingClientRect();
						const buttonRect = event.target.getBoundingClientRect();
						const menuTop = Math.abs(buttonRect.top) + buttonRect.height + 4;
						if (menuTop + menuRect.height > parentRect.top + parentRect.height) {
							const clientHeight = parentRect.height + menuTop + menuRect.height - (
								parentRect.top + parentRect.height);
							parent.style.height = clientHeight.toString() + 'px';
						}
					});
				});
			});
		</script>
	@endpush
@endif
