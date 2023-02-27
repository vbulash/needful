@extends('layouts.chain')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Работодатель',
		        'active' => false,
		        'context' => 'employer',
		        'link' => route('employers.index'),
		    ],
		    [
		        'title' => 'Заявки на практику',
		        'active' => false,
		        'context' => 'order',
		        'link' => route('employers.orders.index', compact('employer')),
		    ],
		    [
		        'title' => 'Ответы на заявку',
		        'active' => true,
		        'context' => 'answer',
		    ],
		];
		$_employer = App\Models\Employer::findOrFail($employer);
		$_order = $_employer->orders()->find($order);
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<h3 class="block-title fw-semibold">Ответы на заявку на практику &laquo;{{ $_order->getTitle() }}&raquo;</h3>
			<p class='mb-4'>Статус заявки: {{ App\Models\OrderEmployerStatus::getName($_order->pivot->status) }}</p>
			@if (
				$_order->pivot->status != App\Models\OrderEmployerStatus::ACCEPTED->value &&
					$_order->pivot->status != App\Models\OrderEmployerStatus::REJECTED->value)
				<a href="{{ route('employers.orders.reject', compact('employer', 'order')) }}"
					class="btn btn-primary mt-3 mb-3">Отказать учебному заведению</a>
				<a href="{{ route('employers.orders.accept', compact('employer', 'order')) }}"
					class="btn btn-primary mt-3 mb-3">Принять заявку учебного заведения</a>
				<p><small>Полная приёмка заявки - без корректировки ответов в таблице ниже<br />
						Частичная приёмка заявки - с корректировкой поля &laquo;Согласны принять&raquo; в таблице ниже
					</small></p>
			@else
				<p><small>
						Статус заявки &laquo;{{ App\Models\OrderEmployerStatus::getName($_order->pivot->status) }}&raquo; конечный - ответ
						(количество практикантов, которое вы согласны принять) уже нельзя отредактировать
					</small></p>
			@endif
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="answers_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Название специальности</th>
							<th>Запрос практикантов</th>
							<th>Согласны принять</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Ответов на заявку на практику для текущего работодателя пока нет...</p>
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
					ajax: '{!! route('employers.orders.answers.index.data', ['employer' => $employer, 'order' => $order]) !!}',
					responsive: true,
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'name',
							name: 'name',
							responsivePriority: 1
						},
						{
							data: 'quantity',
							name: 'quantity',
							responsivePriority: 1
						},
						{
							data: 'approved',
							name: 'approved',
							responsivePriority: 1
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
