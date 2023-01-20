@extends('orders.steps.wizard')

@section('service')
	Создание заявки на практику
@endsection

@section('interior.header')
	<div class="block-header block-header-default">
		<div>
			<button type="button" class="btn btn-primary mt-3 mb-3" id="add-specialty" data-bs-toggle="modal"
				data-bs-target="#specialties-list">
				Добавить специальность к заявке на практику
			</button>
			<p id="no-enabled-data" style="display: none;">Все специальности учебного заведения внесены в заявку на практику</p>
		</div>
	</div>
@endsection

@section('interior.subheader')
@endsection

@section('form.fields')
	@php
		$fields = [];
	@endphp
@endsection

@section('form.body')
	<div class="block-content p-4">
		<div id="table-data" style="display:none;">
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="specialties_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Название специальности</th>
							<th>Количество практикантов</th>
							<th>Действия</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<div id="no-data" style="display:none;">
			<p>Специальностей в заявке на практику пока нет...</p>
		</div>
		<input type="hidden" name="specs" id="specs">
	</div>

	<div class="modal fade" id="specialties-list" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
		data-bs-keyboard="false">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Выбор специальности для заявки на практику</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				</div>
				<div class="modal-body">
					<div class="mb-4">
						<select name="specialties" class="select2 form-control" style="width:100%;" id="specialties"></select>
					</div>
					<div class="form-floating">
						<input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1">
						<label for="quantity">Количество позиций по специальности в заявке</label>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal-close">Закрыть</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="link-specialty">Зафиксировать в
						заявке</button>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/datatables.js') }}"></script>
	<script>
		function createArray(map) {
			let arr = [];
			for (let item of map.values())
				arr.push({
					'id': item.id,
					'text': item.text,
					'quantity': item.quantity,
					'action': item.action
				});
			return arr;
		}

		function reloadSelect() {
			if (window.enabled.size === 0) {
				document.getElementById('add-specialty').style.display = 'none';
				document.getElementById('no-enabled-data').style.display = 'block';
			} else {
				document.getElementById('add-specialty').style.display = 'block';
				let select = $('#specialties');
				select.empty().select2({
					language: 'ru',
					dropdownParent: $('#specialties-list'),
					data: window.enabledArray,
					// placeholder: 'Выберите одного или нескольких учащихся из выпадающего списка',
					// sorter: function(data) {
					// 	return data.sort(function(a, b) {
					// 		return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
					// 	});
					// }
				});
				select.trigger('change');
				document.getElementById('no-enabled-data').style.display = 'none';
			}
			document.getElementById('quantity').value = 1;
		}

		function clickDelete(self, id) {
			const key = id;
			const object = window.selected.get(parseInt(key));
			object.quantity = 1;

			debugger
			window.selected.delete(object.id);
			window.selectedArray = createArray(window.selected);

			window.enabled.set(object.id, object);
			window.enabledArray = createArray(window.enabled);

			reloadSelect();
			window.datatable
				.row($(self).parents('tr'))
				.remove()
				.draw();
		}

		document.getElementById('quantity').addEventListener('input', (event) => {
			document.getElementById('link-specialty').disabled = event.target.value == '';
		}, false);

		document.getElementById('core-create').addEventListener('submit', (event) => {
			if (window.selected.size == 0) {
				event.preventDefault();
                event.stopPropagation();
				showToast('error', 'Не выбраны специальности для заявки', false);
			} else {
				document.getElementById('specs').value =
					JSON.stringify(window.selectedArray);
			}
		}, false);

		$('#link-specialty').on('click', (event) => {
			const key = $('#specialties').val();
			const object = window.enabled.get(parseInt(key));
			object.quantity = $('#quantity').val();

			window.enabled.delete(object.id);
			window.enabledArray = createArray(window.enabled);

			window.selected.set(object.id, object);
			window.selectedArray = createArray(window.selected);

			reloadSelect();
			window.datatable.row.add(object).draw();
		});

		$('#specialties-list').on('show.bs.modal', () => {
			// reloadSelect();
		});

		$(function() {
			// Все специальности
			window.all = new Map();
			window.allArray = [];
			let source = JSON.parse({!! json_encode($specialties) !!});
			for (let item of source) {
				let object = {
					'id': item.id,
					'text': item.name,
					'quantity': 0,
					'action': 0
				};
				window.all.set(item.id, object);
				window.allArray.push(object);
			}
			// Выделенные специальности
			window.selected = new Map();
			window.selectedArray = [];
			@if (isset($heap['specialties']))
				source = JSON.parse('{!! json_encode($heap["specialties"]) !!}');
				for (let item of source) {
					let object = {
						'id': item.id,
						'text': item.text,
						'quantity': item.quantity,
						'action': 0
					};
					window.selected.set(item.id, object);
					window.selectedArray.push(object);
				}
			@endif
			// Доступные специальности
			window.enabledArray = [];
			if (window.selected.size == 0) {
				window.enabled = new Map(window.all);
				window.enabledArray = [...window.allArray];
			} else {
				window.enabled = new Map();
				for (let item of window.all.entries()) {
					if (window.selected.get(item.id)) continue;
					let object = {
						'id': item.id,
						'text': item.text,
						'quantity': item.quantity,
						'action': 0
					};
					window.enabled.set(item.id, object);
					window.enabledArray.push(object);
				}
			}

			window.datatable = $('#specialties_table').DataTable({
				language: {
					"url": "{{ asset('lang/ru/datatables.json') }}"
				},
				responsive: true,
				data: window.selectedArray,
				order: [
					[1, 'asc']
				],
				columns: [{
						data: 'id',
						name: 'id',
						responsivePriority: 1
					},
					{
						data: 'text',
						name: 'text',
						responsivePriority: 1
					},
					{
						data: 'quantity',
						name: 'quantity',
						responsivePriority: 1
					},
					{
						data: 'action',
						name: 'action',
						sortable: false,
						responsivePriority: 1,
						className: 'no-wrap dt-actions',
						render: (data, type, row, meta) => {
							let button =
								`<button class="btn btn-primary btn-sm float-left"
									data-toggle="tooltip" data-placement="top" title="Удаление"
									onclick="clickDelete(this, ${row.id})">
									<i class="fas fa-trash-alt"></i>
								</button>
								`;
							return button;
						}
					}
				]
			});

			if (window.all.size == 0) {
				$('#table-data').hide();
				$('#no-data').show();
			} else {
				$('#table-data').show();
				$('#no-data').hide();
			}
			if (window.enabled.size == 0) {
				$('#add-specialty').attr('disabled', true);
			} else {
				$('#add-specialty').removeAttr('disabled');
			}

			reloadSelect();
		});
	</script>
@endpush
