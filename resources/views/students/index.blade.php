@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Практиканты</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Лица</li>
						<li class="breadcrumb-item active" aria-current="page">Практиканты</li>
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
				@can('students.create')
					<a href="{{ route('students.create', ['sid' => session()->getId()]) }}" class="btn btn-primary mt-3 mb-3">Добавить практиканта</a>
				@endcan
			</div>
			<div class="block-content pb-3">
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
								<th>Электронная почта</th>
								<th>Действия</th>
							</tr>
							</thead>
						</table>
					</div>
				@else
					<p>Практикантов пока нет...</p>
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
		function clickDelete(id, name) {
			if(window.confirm('Удалить практиканта "' + name + '" ?')) {
				$.ajax({
					method: 'DELETE',
					url: "{{ route('students.destroy', ['student' => '0']) }}",
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
			window.datatable = $('#students_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('students.index.data') !!}',
				responsive: true,
				columns: [
					{data: 'id', name: 'id', responsivePriority: 1},
					{data: 'fio', name: 'fio', responsivePriority: 1},
					{data: 'birthdate', name: 'birthdate', responsivePriority: 2},
					{data: 'phone', name: 'phone', responsivePriority: 2},
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
@endsection
@endif
