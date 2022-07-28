@extends('layouts.chain')

@section('header') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учащиеся', 'active' => true, 'context' => 'student'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			@hasrole('Администратор')
			<a href="{{ route('students.create', ['sid' => session()->getId()]) }}"
			   class="btn btn-primary mt-3 mb-3">Добавить учащегося</a>
			@endhasrole

			<p>Красным цветом выделены неактивные учащиеся. Активацию объектов выполняет администратор
				платформы<br/>
				Работа с неактивными объектами ограничена только изменением / просмотром / удалением</p>
		</div>

		<h3 class="block-title">
			@if(isset($ids))
				<br/>
				<small>Отображаются только записи учащихся, доступные текущему пользователю</small>
			@endif
		</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="students_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>Фамилия, имя и отчество</th>
						<th>Дата рождения</th>
						<th>Телефон</th>
						<th>Специальности</th>
						<th>Пользователь</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Учащихся пока нет...</p>
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
					url: "{{ route('students.destroy', ['student' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить учащегося &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#students_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					@if(isset($ids))
					ajax: '{!! route('students.index.data', ['ids' => $ids]) !!}',
					@else
					ajax: '{!! route('students.index.data') !!}',
					@endif
					responsive: true,
					createdRow: function (row, data, dataIndex) {
						if (data.status === 0)
							row.style.color = 'red';
					},
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'fio', name: 'fio', responsivePriority: 1},
						{data: 'birthdate', name: 'birthdate', responsivePriority: 2},
						{data: 'phone', name: 'phone', responsivePriority: 2},
						{
							data: 'specialties', name: 'specialties', responsivePriority: 3, render: (data) => {
								return data.join(",<br/>");
							}
						},
						{data: 'link', name: 'link', responsivePriority: 3},
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
