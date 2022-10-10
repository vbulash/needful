@extends('layouts.chain')

@section('service')
	Работа с работодателями
	@if (isset(session('context')['chain']))
		(только цепочка значений)
	@endif
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Работодатель', 'active' => false, 'context' => 'employer', 'link' => route('employers.index', ['sid' => session()->getId()])],
			['title' => 'Стажировка', 'active' => false, 'context' => 'internship', 'link' => route('internships.index', ['sid' => session()->getId()])],
			['title' => 'График стажировки', 'active' => true, 'context' => 'timetable'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			@if (isset(session('context')['chain']))
				Отображается единственная запись по цепочке из входящего сообщения
			@else
				<a href="{{ route('timetables.create', ['sid' => session()->getId()]) }}"
				   class="btn btn-primary mt-3 mb-3">Добавить запись графика стажировки</a>
				<p>Вы также можете перейти на список специальностей по стажировке по ссылке
					<a href="{{ route('especialties.index', ['sid' => session()->getId()]) }}">Список специальностей стажировки</a></p>
			@endif
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="timetables_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th>Начало</th>
						<th>Завершение</th>
						<th>Наименование записи графика стажировки</th>
						<th>Требуется практикантов</th>
						<th>Действия</th>
					</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Графиков стажировки пока нет...</p>
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
				url: "{{ route('timetables.destroy', ['timetable' => '0']) }}",
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
			document.getElementById('confirm-body').innerHTML = "Удалить запись графика стажировки № " + id + " ?";
			document.getElementById('confirm-yes').dataset.id = id;
			let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
			confirmDialog.show();
		}

		$(function () {
			window.datatable = $('#timetables_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('timetables.index.data', ['internship' => $internship->getKey()]) !!}',
				responsive: true,
				columns: [
					{data: 'id', name: 'id', responsivePriority: 1},
					{data: 'start', name: 'start', responsivePriority: 2},
					{data: 'end', name: 'end', responsivePriority: 2},
					{data: 'name', name: 'name', responsivePriority: 3},
					{data: 'planned', name: 'planned', responsivePriority: 2, className: 'dt-center'},
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
