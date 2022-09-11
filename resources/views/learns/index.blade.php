@extends('layouts.chain')

@section('header') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учащийся', 'active' => false, 'context' => 'student', 'link' => route('students.index', ['sid' => session()->getId()])],
			['title' => 'История обучения', 'active' => true, 'context' => 'learn'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<a href="{{ route('learns.create', ['sid' => session()->getId()]) }}"
			   class="btn btn-primary mt-3 mb-3">Добавить запись истории обучения</a>

			<p>Красным цветом выделены неактивные записи обучения. Активацию объектов выполняет администратор
				платформы<br/>
				Работа с неактивными объектами ограничена только изменением / просмотром / удалением</p>
		</div>

		<h3 class="block-title">
		</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="learns_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>Дата поступления</th>
						<th>Дата завершения</th>
						<th>Учебное заведение</th>
						<th>Специальность</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Записей истории обучения пока нет...</p>
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
					url: "{{ route('learns.destroy', ['learn' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить запись истории обучения &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#learns_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('learns.index.data') !!}',
					responsive: true,
					createdRow: function (row, data, dataIndex) {
						if (data.status === 0)
							row.style.color = 'red';
					},
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'start', name: 'fio', responsivePriority: 2},
						{data: 'finish', name: 'birthdate', responsivePriority: 2},
						{data: 'school', name: 'phone', responsivePriority: 2},
						{data: 'specialty', name: 'email', responsivePriority: 3},
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
