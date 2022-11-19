@extends('services.service')

@section('service')
	Работодатель. Создать практику
@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Выбор работодателя', 'active' => false, 'context' => 'employer', 'link' => route('e2s.start_internship.step1', ['sid' => session()->getId()])],
			['title' => 'Выбор практики', 'active' => false, 'context' => 'internship', 'link' => route('e2s.start_internship.step2', ['sid' => session()->getId()])],
			['title' => 'Выбор графика практики', 'active' => false, 'context' => 'timetable', 'link' => route('e2s.start_internship.step3', ['sid' => session()->getId()])],
			['title' => 'Выбор практикантов', 'active' => true, 'context' => null],
			['title' => 'Выбор руководителя практики', 'active' => false, 'context' => 'teacher'],
			['title' => 'Подтверждение выбора', 'active' => false],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<h3 class="block-title">Выбор практикантов</h3>
			<p><small>В таблице практикантов ниже вы можете выбрать несколько практикантов (только в активном статусе) простым кликом мыши. Повторный
					клик убирает практиканта из выборки</small></p>
			<p><small><strong>Текущие выбранные практиканты:</strong></small></p>
			<p><small><span id="note">Нет</span></small></p>
			<form role="form" method="post" id="form-select"
				  action="{{ route('e2s.start_internship.step4.select', ['sid' => $sid]) }}">
				@csrf
				<button type="submit" id="select" class="btn btn-primary mb-3" disabled>Зафиксировать данный список как окончательный</button>
				<input type="hidden" name="ids" id="ids">
				<input type="hidden" name="names" id="names">
			</form>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered text-nowrap" id="students_table"
					   style="width: 100%;">
					<thead>
					<tr>
						<th style="width: 30px">#</th>
						<th style="display: none">&nbsp;</th>
						<th style="display: none">&nbsp;</th>
						<th style="display: none">&nbsp;</th>
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
@endsection

@if ($count > 0)
	@push('css_after')
		<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
	@endpush

	@push('js_after')
		<script src="{{ asset('js/datatables.js') }}"></script>
		<script>
			$(function () {
				window.select = new Map();

				window.datatable = $('#students_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('e2s.start_internship.step4.data') !!}',
					responsive: true,
					select: {
						style: 'multi'
					},
					order: [[1, 'asc'], [2, 'asc'], [3, 'asc']],
					columns: [
						{data: 'id', name: 'id', responsivePriority: 1},
						{data: 'lastname', name: 'lastname', visible: false},
						{data: 'firstname', name: 'firstname', visible: false},
						{data: 'surname', name: 'surname', visible: false},
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

				$('#form-select').on('submit', event => {
					let keys = Array.from(window.select.keys());
					$('#ids').val(JSON.stringify(keys));
					$('#names').val((Array.from(window.select.values()).sort().join(', ')));
				});

				window.datatable.on('select', (event, dt, type, indexes) => {
					if (type === 'row') {
						let data = window.datatable.rows(indexes).data();
						let id = data.pluck('id')[0];
						let name = data.pluck('fio')[0];
						if (window.select.get(id) === undefined) {
							window.select.set(id, name);
							$('#note').html((Array.from(window.select.values()).sort().join(', ')));
						}
						$('#select').prop('disabled', false);
					}
				});

				window.datatable.on('deselect', (event, dt, type, indexes) => {
					if (type === 'row') {
						let data = window.datatable.rows(indexes).data();
						let id = data.pluck('id')[0];
						if (window.select.delete(id)) {
							$('#note').html(Array.from(window.select.values()).join(', '));
						}
						$('#select').prop('disabled', window.select.size === 0);
						if (window.select.size === 0) {
							$('#note').html('Нет');
							$('#select').prop('disabled', true);
						}
					}
				});
			});
		</script>
	@endpush
@endif
