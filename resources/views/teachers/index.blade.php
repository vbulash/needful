@extends('layouts.chain')

@section('service')
	Работа с руководителями практики
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Руководители практики', 'active' => true, 'context' => 'teacher', 'link' => route('teachers.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<a href="{{ route('teachers.create', ['sid' => session()->getId()]) }}"
			   class="btn btn-primary mt-3 mb-3">Добавить руководителя практики</a>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="teachers_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>ФИО руководителя практики</th>
						<th>Руководитель практики работает в</th>
						<th>Должность</th>
						<th>Телефон</th>
						<th>Электронная почта</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Руководителя практики пока нет...</p>
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

			document.getElementById('confirm-yes').addEventListener('click', (event) => {
				$.ajax({
					method: 'DELETE',
					url: "{{ route('teachers.destroy', ['teacher' => '0']) }}",
					data: {
						id: event.target.dataset.id,
					},
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}, false);

			function clickDelete(id, name) {
				document.getElementById('confirm-title').innerText = "Подтвердите удаление";
				document.getElementById('confirm-body').innerHTML = "Удалить руководителя практики &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#teachers_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('teachers.index.data', ['sid' => session()->getId()]) !!}',
					responsive: true,
					createdRow: function (row, data, dataIndex) {
						if (data.status === 0)
							row.style.color = 'red';
					},
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'name', name: 'name', responsivePriority: 1},
						{data: 'worksin', name: 'worksin', responsivePriority: 2},
						{data: 'position', name: 'position', responsivePriority: 3},
						{data: 'phone', name: 'phone', responsivePriority: 3},
						{data: 'email', name: 'email', responsivePriority: 3},
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
