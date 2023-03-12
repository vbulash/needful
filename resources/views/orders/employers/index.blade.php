@extends('layouts.chain')

@section('service')
	Работа с заявками на практику
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Заявки на практику', 'active' => false, 'context' => 'order', 'link' => route('orders.index')], ['title' => 'Детали заявки<br/>Уведомления работодателям', 'active' => true, 'context' => 'order.employer']];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<h3 class="block-title fw-semibold">Уведомления работодателям</h3>
			<button type="button" class="btn btn-primary mt-3 mb-3" id="add-employer" data-bs-toggle="modal"
				data-bs-target="#employers-list">
				Добавить работодателя к заявке на практику
			</button>
			<p>Вы также можете перейти на детали заявки по ссылке <a
					href="{{ route('order.specialties.index', ['order' => $order]) }}">Детали заявки</a></p>
			<p id="no-enabled-data" style="display: none;">Все работодатели внесены в заявку на практику</p>
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="order_employers_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px"># работодателя</th>
							<th>Название работодателя</th>
							<th>Статус уведомления</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Работодателей в заявке на практику пока нет...</p>
		@endif
	</div>

	<div class="modal fade" id="employers-list" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
		data-bs-keyboard="false">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Выбор работодателя для заявки на практику</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				</div>
				<div class="modal-body">
					<p>Здесь доступны все работодатели, даже если у них нет пересечения по специальностям с образовательным учреждением.
					</p>
					<div class="mb-4">
						<select name="employers" class="select2 form-control" style="width:100%;" id="employers"></select>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal-close">Закрыть</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="link-employer">Зафиксировать в
						заявке</button>
				</div>
			</div>
		</div>
	</div>
@endsection

@if ($count > 0)
	@push('css_after')
		<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
	@endpush

	@push('js_after')
		<script src="{{ asset('js/datatables.js') }}"></script>
		<script>
			function reloadSelect() {
				if (Object.keys(window.enabled).length === 0) {
					document.getElementById('add-employer').style.display = 'none';
					document.getElementById('no-enabled-data').style.display = 'block';
				} else {
					document.getElementById('add-employer').style.display = 'block';
					let select = $('#employers');

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
						dropdownParent: $('#employers-list'),
						data: data,
						// placeholder: 'Выберите одного или нескольких учащихся из выпадающего списка',
						// sorter: function(data) {
						// 	return data.sort(function(a, b) {
						// 		return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
						// 	});
						// }
					});
					//select.val('').trigger('change');
					document.getElementById('no-enabled-data').style.display = 'none';
				}
			}

			document.getElementById('confirm-yes').addEventListener('click', (event) => {
				$.ajax({
					method: 'DELETE',
					url: "{{ route('order.employers.destroy', ['order' => $order, 'employer' => '0']) }}",
					data: {
						id: event.target.dataset.id,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						const id = event.target.dataset.employer;
						const text = window.selected[id];
						delete window.selected[id];
						window.enabled[id] = text;

						reloadSelect();
						window.datatable.ajax.reload();
					}
				});
			}, false);

			function clickDelete(id, name, employer) {
				document.getElementById('confirm-title').innerText = "Подтвердите удаление";
				document.getElementById('confirm-body').innerHTML = "Удалить работодателя из заявки на практику &laquo;" + name +
					"&raquo; ?";
				document.getElementById('confirm-yes').dataset.id = id;
				document.getElementById('confirm-yes').dataset.employer = employer;
				let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
				confirmDialog.show();
			}

			function clickMail(order_employer_id) {
				$.ajax({
					method: 'POST',
					url: "{{ route('order.employers.mail') }}",
					data: {
						order_employer: order_employer_id,
						repeat: true,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}

			$(function() {
				window.datatable = $('#order_employers_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: "{!! route('order.employers.index.data', ['order' => $order]) !!}",
					responsive: true,
					order: [
						[1, 'asc']
					],
					columns: [{
							data: 'id',
							name: 'id',
							responsivePriority: 1
						},
						{
							data: 'name',
							name: 'name',
							responsivePriority: 1
						},
						{
							data: 'status',
							name: 'status',
							responsivePriority: 1
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

				$('#link-employer').on('click', (event) => {
					const id = $('#employers').val();
					const text = window.enabled[id];

					$.ajax({
						method: 'POST',
						url: "{{ route('order.employers.store', ['order' => $order]) }}",
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
			});
		</script>
	@endpush
@endif
