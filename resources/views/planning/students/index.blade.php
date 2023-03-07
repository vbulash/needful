@extends('layouts.chain')

@section('service')
	Планирование практикантов по заявкам на практику от образовательных учреждений
@endsection

@section('steps')
	@php
		$context = session('context');
		$order = \App\Models\Order::find($context['order']);
		$school = $order->school;
		$answer = \App\Models\Answer::find($context['answer']);
		$employer = $answer->employer;
		$specialty = $answer->orderSpecialty->specialty;
		$steps = [['title' => 'Заявки на практику', 'active' => false, 'context' => 'order', 'link' => route('planning.orders.index')], ['title' => 'Ответы работодателей', 'active' => false, 'context' => 'answer', 'link' => route('planning.answers.index', ['order' => $context['order']])], ['title' => 'Практиканты', 'active' => true, 'context' => 'answer.students']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<button type="button" class="btn btn-primary mt-3 mb-3" id="add-student" data-bs-toggle="modal"
				data-bs-target="#students-list">
				Добавить практиканта
			</button>
			<p>
				<small>
					Добавить в заявку на практику можно только учащихся образовательного учреждения
					&laquo;{{ $school->getTitle() }}&raquo;
				</small>
			</p>
			<p id="no-enabled-data" style="display: none;">Все учащиеся образовательного учреждения
				&laquo;{{ $school->getTitle() }}&raquo; распределены</p>

			<div class="d-flex">
				<button type="button" class="btn btn-primary mt-3 mb-3 me-4" id="send-students">
					Уведомить работодателя
				</button>
				<button type="button" class="btn btn-primary mt-3 mb-3" id="fix-students">
					Зафиксировать практикантов
				</button>
			</div>
			<p>
				<small>
					Если количество практикантов (новых или новых + одобренных) станет равным одобренному количеству
					({{ $answer->approved }}), вы сможете уведомить
					работодателя для принятия решения по персоналиям практикантов.<br />
					Если количество одобренных практикантов станет равным {{ $answer->approved }}, вы сможете окончательно зафиксировать
					практикантов в заявке по специальности
					&laquo;{{ $specialty->name }}&raquo; по работодателю &laquo{{ $employer->getTitle() }};&raquo;.
				</small>
			</p>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="students_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Фамилия, имя и отчество</th>
							<th>Дата рождения</th>
							<th>Телефон</th>
							<th>Электронная почта</th>
							<th>Статус практиканта</th>
							<th>Действия</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Практикантов пока нет...</p>
		@endif
	</div>

	<div class="modal fade" id="students-list" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
		data-bs-keyboard="false">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Выбор практиканта из учащихся образовательного учреждения</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				</div>
				<div class="modal-body">
					<div class="mb-4">
						<select name="students" class="select2 form-control" style="width:100%;" id="students"></select>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal-close">Закрыть</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="link-student">Зафиксировать</button>
				</div>
			</div>
		</div>
	</div>
@endsection

{{-- @if ($count > 0) --}}
@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/datatables.js') }}"></script>
	<script>
		function reloadSelect() {
			if (Object.keys(window.enabled).length === 0) {
				document.getElementById('add-student').style.display = 'none';
				document.getElementById('no-enabled-data').style.display = 'block';
			} else {
				document.getElementById('add-student').style.display = 'block';
				let select = $('#students');

				const data = [];
				for (let object in window.enabled)
					data.push({
						'id': object,
						'text': window.enabled[object]
					});
				data.sort((a, b) => {
					if (a.text === b.text) return 0;
					else if (a.text > b.text) return 1;
					else return -1;
				});
				select.empty().select2({
					language: 'ru',
					dropdownParent: $('#students-list'),
					data: data,
				});
				//select.val('').trigger('change');
				document.getElementById('no-enabled-data').style.display = 'none';
			}

			document.getElementById('send-students').disabled = Object.keys(window.selected).length != {{ $answer->approved }};
		}

		function clickDelete(student) {
			$.ajax({
				method: 'DELETE',
				url: "{{ route('planning.students.destroy', ['answer' => $answer->getKey()]) }}",
				data: {
					id: student,
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: () => {
					const id = student;
					const text = window.selected[id];
					delete window.selected[id];
					window.enabled[id] = text;

					reloadSelect();
					window.datatable.ajax.reload();
				}
			});
		}

		$(function() {
			window.datatable = $('#students_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				processing: true,
				serverSide: true,
				ajax: '{!! route('planning.students.index.data') !!}',
				responsive: true,
				createdRow: function(row, data, dataIndex) {
					if (data.status === 0)
						row.style.color = 'red';
				},
				columns: [{
						data: 'id',
						name: 'id',
						responsivePriority: 1
					},
					{
						data: 'fio',
						name: 'fio',
						responsivePriority: 2
					},
					{
						data: 'birthdate',
						name: 'birthdate',
						responsivePriority: 3
					},
					{
						data: 'phone',
						name: 'phone',
						responsivePriority: 3
					},
					{
						data: 'email',
						name: 'email',
						responsivePriority: 3
					},
					{
						data: 'status',
						name: 'status',
						responsivePriority: 2
					},
					{
						data: 'action',
						name: 'action',
						sortable: false,
						responsivePriority: 1,
						className: 'no-wrap dt-actions'
					}
				]
			});

			window.datatable.on('draw', function() {
				$('.dropdown-toggle.actions').on('shown.bs.dropdown', (event) => {
					const menu = event.target.parentElement.querySelector('.dropdown-menu');
					let parent = menu.closest('.dataTables_wrapper');
					const parentRect = parent.getBoundingClientRect();
					parentRect.top = Math.abs(parentRect.top);
					const menuRect = menu.getBoundingClientRect();
					const buttonRect = event.target.getBoundingClientRect();
					const menuTop = Math.abs(buttonRect.top) + buttonRect.height + 4;
					if (menuTop + menuRect.height > parentRect.top + parentRect.height) {
						const clientHeight = parentRect.height + menuTop + menuRect.height - (
							parentRect.top + parentRect.height);
						parent.style.height = clientHeight.toString() + 'px';
					}
				});
			});

			window.selected = {!! json_encode($selected) !!};
			window.enabled = {!! json_encode($enabled) !!};
			reloadSelect();

			$('#link-student').on('click', (event) => {
				const id = $('#students').val();
				const text = window.enabled[id];

				$.ajax({
					method: 'POST',
					url: "{{ route('planning.students.store') }}",
					data: {
						id: id,
						text: text,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						delete window.enabled[id];
						window.selected[id] = text;

						reloadSelect();
						window.datatable.ajax.reload();
					}
				});
			});

			$('#send-students').on('click', (event) => {
				$.ajax({
					method: 'POST',
					url: "{{ route('planning.students.send', ['answer' => $answer->getKey()]) }}",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			});
		});
	</script>
@endpush
{{-- @endif --}}
