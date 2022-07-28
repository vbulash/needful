@extends('layouts.chain')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => true, 'context' => 'employer'],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship'],
			['title' => 'График стажировки или Специальности для стажировки', 'active' => false, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			@hasrole('Администратор')
			<a href="{{ route('employers.create', ['sid' => session()->getId()]) }}"
			   class="btn btn-primary mt-3 mb-3">Добавить работодателя</a>
			@endhasrole

			<p>Красным цветом выделены неактивные работодатели. Активацию объектов выполняет администратор платформы<br/>
			Работа с неактивными объектами ограничена только изменением / просмотром / удалением</p>
		</div>

		<h3 class="block-title">
			@if(isset($ids))
				<br/>
				<small>Отображаются только записи работодателей, доступные текущему пользователю</small>
			@endif
		</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="employers_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>ИНН</th>
						<th>Наименование</th>
						<th>Почтовый адрес</th>
						<th>Телефон</th>
						<th>Электронная почта</th>
						<th>Связан с пользователем</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Работодателей пока нет...</p>
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
					url: "{{ route('employers.destroy', ['employer' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить работодателя &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#employers_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					@if(isset($ids))
					ajax: '{!! route('employers.index.data', ['ids' => $ids, 'sid' => session()->getId()]) !!}',
					@else
					ajax: '{!! route('employers.index.data', ['sid' => session()->getId()]) !!}',
					@endif
					createdRow: function (row, data, dataIndex) {
						if (data.status === 0)
							row.style.color = 'red';
					},
					responsive: true,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'inn', name: 'inn', responsivePriority: 1},
						{data: 'name', name: 'name', responsivePriority: 2},
						{data: 'post_address', name: 'post_address', responsivePriority: 3},
						{data: 'phone', name: 'phone', responsivePriority: 3},
						{data: 'email', name: 'email', responsivePriority: 2},
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
