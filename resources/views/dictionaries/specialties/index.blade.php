@extends('layouts.chain')

@section('service') @endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Специальность', 'active' => true, 'context' => 'specialty'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<a href="{{ route('specialties.create', ['sid' => session()->getId()]) }}"
		   class="btn btn-primary mt-3 mb-3">Добавить специальность</a>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="specialties_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>Название специальности</th>
						<th>Тип записи</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Специальностей пока нет...</p>
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
				url: "{{ route('specialties.destroy', ['specialty' => '0']) }}",
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
			document.getElementById('confirm-body').innerHTML = "Удалить специальность \"" + name + "\" ?";
			document.getElementById('confirm-yes').dataset.id = id;
			let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
			confirmDialog.show();
		}

		$(function () {
			window.datatable = $('#specialties_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('specialties.index.data') !!}',
				responsive: true,
				pageLength: 100,
				columns: [
					{data: 'id', name: 'id', responsivePriority: 1},
					{data: 'name', name: 'name', responsivePriority: 1},
					{data: 'federal', name: 'federal', responsivePriority: 2},
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
