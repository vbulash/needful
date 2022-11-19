@extends('layouts.chain')

@section('service')
	Работа с работодателями
	@if (isset(session('context')['chain']))
		(только цепочка значений)
	@endif
@endsection

@section('steps')
	@php
		if (isset(session('context')['chain']))
			$title = 'График практики';
		else
			$title = 'График практики или Специальности для практики';
		$steps = [
			['title' => 'Работодатель', 'active' => true, 'context' => 'employer'],
			['title' => 'Практика', 'active' => false, 'context' => 'internship'],
			['title' => $title, 'active' => false, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			@if (isset(session('context')['chain']))
				Отображается единственная запись по цепочке из входящего сообщения
			@else
				@hasrole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)
				<a href="{{ route('employers.create', ['sid' => session()->getId()]) }}"
				   class="btn btn-primary mt-3 mb-3">Добавить работодателя</a>
				@endhasrole

				<p>Красным цветом выделены неактивные работодатели. Активацию объектов выполняет администратор платформы<br/>
					Работа с неактивными объектами ограничена только изменением / просмотром / удалением</p>
			@endif
		</div>
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
					ajax: '{!! route('employers.index.data') !!}',
					createdRow: function (row, data, dataIndex) {
						if (data.status === 0)
							row.style.color = 'red';
					},
					responsive: true,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'inn', name: 'inn', responsivePriority: 1},
						{data: 'short', name: 'short', responsivePriority: 2},
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
