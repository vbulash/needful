@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Работодатели</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Лица</li>
						<li class="breadcrumb-item active" aria-current="page">Работодатели</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<!-- Table -->
		<div class="block block-rounded">
			<div class="block-header block-header-default">
				@can('users.create')
					<a href="{{ route('employers.create', ['sid' => session()->getId()]) }}" class="btn btn-primary mt-3 mb-3">Добавить работодателя</a>
				@endcan
			</div>
			<div class="block-content pb-3">
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
								<th>Действия</th>
							</tr>
							</thead>
						</table>
					</div>
				@else
					<p>Работодателей пока нет...</p>
				@endif
			</div>
		</div>
		<!-- END Table -->
	</div>

@endsection

@if ($count > 0)
@section('css_after')
	<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endsection

@section('js_after')
	<script src="{{ asset('js/datatables.js') }}"></script>
	<script>
		function clickDelete(name) {
			if(window.confirm('Удалить работодателя "' + name + '" ?')) {
				$.ajax({
					method: 'DELETE',
					url: "{{ route('employers.destroy', ['user' => '0']) }}",
					data: {
						id: id,
					},
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}
		}

		$(function () {
			window.datatable = $('#employers_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('employers.index.data') !!}',
				responsive: true,
				columns: [
					{data: 'id', name: 'id', responsivePriority: 1},
					{data: 'inn', name: 'inn', responsivePriority: 1},
					{data: 'name', name: 'name', responsivePriority: 2},
					{data: 'post_address', name: 'post_name', responsivePriority: 3},
					{data: 'phone', name: 'phone', responsivePriority: 3},
					{data: 'email', name: 'email', responsivePriority: 2},
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
@endsection
@endif
