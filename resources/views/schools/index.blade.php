@extends('layouts.chain')

@section('service')
	Работа с учебными заведениями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Учебное заведение', 'active' => true, 'context' => 'school', 'link' => route('schools.index', ['sid' => session()->getId()])],
			['title' => 'Специальность', 'active' => false, 'context' => 'specialty'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		@hasrole('Администратор')
		<a href="{{ route('schools.create', ['sid' => session()->getId()]) }}"
		   class="btn btn-primary mt-3 mb-3">Добавить учебное заведение</a>
		@endhasrole

		<h3 class="block-title">
			@if(isset($ids))
				<br/>
				<small>Отображаются только записи учебных заведений, доступные текущему пользователю</small>
			@endif
		</h3>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="schools_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>Тип учебного заведения</th>
						<th>Наименование</th>
						<th>Телефон</th>
						<th>Электронная почта</th>
						<th>Связан с пользователем</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Учебных заведений пока нет...</p>
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
					url: "{{ route('schools.destroy', ['school' => '0']) }}",
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
				document.getElementById('confirm-body').innerHTML = "Удалить учебное заведение &laquo;" + name + "&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			$(function () {
				window.datatable = $('#schools_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					@if(isset($ids))
					ajax: '{!! route('schools.index.data', ['ids' => $ids, 'sid' => session()->getId()]) !!}',
					@else
					ajax: '{!! route('schools.index.data', ['sid' => session()->getId()]) !!}',
					@endif
					responsive: true,
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'type', name: 'type', responsivePriority: 1},
						{data: 'name', name: 'name', responsivePriority: 1},
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