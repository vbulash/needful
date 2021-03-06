@extends('services.service')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => true, 'context' => 'internship'],
			['title' => 'График стажировки', 'active' => false, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<a href="{{ route('internships.create', ['sid' => session()->getId()]) }}"
		   class="btn btn-primary mt-3 mb-3">Добавить стажировку</a>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="internships_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>Название стажировки</th>
						<th>Тип</th>
						<th>Статус</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Стажировок пока нет...</p>
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
				url: "{{ route('internships.destroy', ['internship' => '0']) }}",
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
			document.getElementById('confirm-body').innerHTML = "Удалить стажировку &laquo;" + name + "&raquo; ?";
			document.getElementById('confirm-yes').dataset.id = id;
			let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
			confirmDialog.show();
		}

		$(function () {
			window.datatable = $('#internships_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('internships.index.data', ['employer' => $employer->getKey()]) !!}',
				responsive: true,
				columns: [
					{data: 'id', name: 'id', responsivePriority: 1},
					{data: 'iname', name: 'iname', responsivePriority: 2},
					{data: 'itype', name: 'itype', responsivePriority: 3},
					{data: 'status', name: 'status', responsivePriority: 3},
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
