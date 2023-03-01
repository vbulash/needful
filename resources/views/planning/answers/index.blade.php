@extends('layouts.chain')

@section('service')
	Планирование практикантов по заявкам на практику от образовательных учреждений
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Заявки на практику', 'active' => false, 'context' => 'order', 'link' => route('planning.orders.index')], ['title' => 'Ответы работодателей', 'active' => true, 'context' => 'answer'], ['title' => 'Практиканты', 'active' => false, 'context' => 'answer.students']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<h3 class="block-title fw-semibold mb-4">Ответы работодателей</h3>
			<p><small>Отображаются ответы работодателей только в случае утверждения работодателем заявки на практику</small></p>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="answers_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Работодатель</th>
							<th>Специальность</th>
							<th>Одобренных практикантов</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Ответов работодателей пока нет - ожидайте утверждения работодателем заявок в целом (специальности + количества).
			</p>
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
				window.datatable = $('#answers_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('planning.answers.index.data', ['order' => $order]) !!}',
					responsive: true,
					columns: [{
							data: 'aid',
							name: 'aid',
							responsivePriority: 1
						},
						{
							data: 'employer',
							name: 'employer',
							responsivePriority: 3
						},
						{
							data: 'specialty',
							name: 'specialty',
							responsivePriority: 2
						},
						{
							data: 'approved',
							name: 'approved',
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
