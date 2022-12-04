@extends('orders.steps.wizard')

@section('service')
	Создание заявки на практику
@endsection

@section('form.fields')
	@php
		$fields = [];
	@endphp
@endsection

@section('interior')
	{{-- <div class="block-header block-header-default">
		<h3 class="block-title">
			Выберите учебное заведение для создания заявки на практику
		</h3>
	</div> --}}
	<div class="block-content p-4">
		<div id="table-data">
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="specialties_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Название специальности</th>
							<th>Количество практикантов</th>
							<th>Действия</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<div id="no-data">
			<p>Специальностей в заявке на практику пока нет...</p>
		</div>
	</div>
@endsection

@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/datatables.js') }}"></script>
	<script>
		$(function() {
			// Все специальности
			window.all = new Map();
			window.allArray = [];
			let source = JSON.parse('{!! json_encode($specialties) !!}');
			for (let item of source) {
				let object = {
					'id': item.id,
					'name': item.name,
					'quantity': 0,
					'action': 0
				};
				window.all.set(item.id, object);
				window.allArray.push(object);
			}
			// Выделенные специальности
			window.selected = new Map();
			window.selectedArray = [];
			@if (isset($heap['specialties']))
				source = JSON.parse('{!! json_encode($heap['specialties']) !!}');
				for (let item of source) {
					let object = {
						'id': item.id,
						'name': item.name,
						'quantity': item.quantity,
						'action': 0
					};
					window.selected.set(item.id, object);
					window.selectedArray.push(object);
				}
			@endif

			window.datatable = $('#specialties_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				responsive: true,
				data: window.selectedArray,
				order: [
					[1, 'asc']
				],
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
						data: 'action',
						name: 'action',
						sortable: false,
						responsivePriority: 1,
						className: 'no-wrap dt-actions',
						render: (data, type, row, meta) => {
							let button =
								`<a href="javascript:void(0)" class="btn btn-primary btn-sm float-left"
									data-toggle="tooltip" data-placement="top" title="Удаление"
									onclick="clickDelete(${row.id}, '${row.name}')">
									<i class="fas fa-trash-alt"></i>
								</a>
								`;
							return button;
						}
					}
				]
			});
		});
	</script>
@endpush
