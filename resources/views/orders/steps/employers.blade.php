@extends('orders.steps.wizard')

@section('service')
	Создание заявки на практику
@endsection

@section('interior.header')
	<div class="block-header block-header-default">
		<div>
			<p>Здесь отображаются работодатели, полностью либо частично пересекающиеся по списку специальностей с ранее выбранным учебным заведением.<br/>
				Если список работодателей пуст - пересечения по специальностям нет. Но вы можете откорректировать список специальностей работодателя.<br/>
				Выбранные здесь работодатели при завершении создания заявки на практику будут уведомлены о заявке на практику</p>
			<button type="button" class="btn btn-primary mt-3 mb-3" id="add-employer" data-bs-toggle="modal"
				data-bs-target="#employers-list">
				Добавить работодателя к заявке на практику
			</button>
			<p id="no-enabled-data" style="display: none;">Все работодатели внесены в заявку на практику</p>
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
				<table class="table table-bordered table-hover text-nowrap" id="employers_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Название работодателя</th>
							<th>Действия</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<div id="no-data" style="display:none;">
			<p>Работодателей в заявке на практику пока нет...</p>
		</div>
		<input type="hidden" name="emps" id="emps">
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
					'action': item.action
				});
			return arr;
		}

		function reloadSelect() {
			if (window.enabled.size === 0) {
				document.getElementById('add-employer').style.display = 'none';
				document.getElementById('no-enabled-data').style.display = 'block';
			} else {
				document.getElementById('add-employer').style.display = 'block';
				let select = $('#employers');
				select.empty().select2({
					language: 'ru',
					dropdownParent: $('#employers-list'),
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

		document.getElementById('core-create').addEventListener('submit', (event) => {
			if (window.selected.size == 0) {
				event.preventDefault();
                event.stopPropagation();
				showToast('error', 'Не выбран работодатель для заявки', false);
			} else {
				document.getElementById('emps').value =
					JSON.stringify(window.selectedArray);
			}
		}, false);

		$('#link-employer').on('click', (event) => {
			const key = $('#employers').val();
			const object = window.enabled.get(parseInt(key));

			window.enabled.delete(object.id);
			window.enabledArray = createArray(window.enabled);

			window.selected.set(object.id, object);
			window.selectedArray = createArray(window.selected);

			reloadSelect();
			window.datatable.row.add(object).draw();
		});

		$('#employers-list').on('show.bs.modal', () => {
			// reloadSelect();
		});

		$(function() {
			// Все работодатели
			window.all = new Map();
			window.allArray = [];
			let source = JSON.parse({!! json_encode($employers) !!});
			for (let item of source) {
				let object = {
					'id': item.id,
					'text': item.name,
					'action': 0
				};
				window.all.set(item.id, object);
				window.allArray.push(object);
			}
			// Выделенные работодатели
			window.selected = new Map();
			window.selectedArray = [];
			@if (isset($heap['employers']))
				source = JSON.parse({!! json_encode($heap['employers']) !!});
				for (let item of source) {
					let object = {
						'id': item.id,
						'text': item.name,
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
						'action': 0
					};
					window.enabled.set(item.id, object);
					window.enabledArray.push(object);
				}
			}

			window.datatable = $('#employers_table').DataTable({
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
				$('#add-employer').attr('disabled', true);
			} else {
				$('#add-employer').removeAttr('disabled');
			}

			reloadSelect();
		});
	</script>
@endpush
