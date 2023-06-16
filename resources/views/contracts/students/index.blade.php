@extends('layouts.chain')

@section('service')
	Работа с договорами на практику
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Договор на практику',
		        'active' => false,
		        'context' => 'contract',
				'link' => route('contracts.index'),
		    ],
			[
		        'title' => 'Практиканты',
		        'active' => true,
		        'context' => 'contract.students',
		    ],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<h3 class="block-title fw-semibold mb-4">Практиканты</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="students_table" style="width: 100%;">
					<thead>
						<tr>
							<th>Специальность</th>
							<th>Практикант</th>
							<th>Телефон</th>
							<th>Электронная почта</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Договоров на практику пока нет...</p>
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
				window.datatable = $('#students_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: "{!! route('contracts.students.index.data', ['contract' => $contract]) !!}",
					responsive: true,
					columns: [
						{
							data: 'specialty',
							name: 'specialty',
							responsivePriority: 1
						},
						{
							data: 'student',
							name: 'student',
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
