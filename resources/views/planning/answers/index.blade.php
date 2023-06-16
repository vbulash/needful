@extends('layouts.chain')

@section('service')
	Планирование практикантов по заявкам на практику от образовательных учреждений
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Заявки на практику',
		        'active' => false,
		        'context' => 'order',
		        'link' => route('planning.orders.index'),
		    ],
		    ['title' => 'Ответы работодателей', 'active' => true, 'context' => 'answer'],
		    ['title' => 'Практиканты', 'active' => false, 'context' => 'answer.students'],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<h3 class="block-title fw-semibold mb-4">Ответы работодателей</h3>
			<p><small>Отображаются ответы работодателей только в случае утверждения работодателем заявки на практику</small></p>
			@if (count($ready) > 0)
				<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sign-contract">Заключить договор с
					работодателем</button>
			@endif
		</div>
	</div>
	<div class="block-content p-4">
		@if ($count > 0)
			<div class="table-responsive">
				<table class="table table-bordered table-hover text-nowrap" id="answers_table" style="width: 100%;">
					<thead>
						<tr>
							<th style="width: 30px">#</th>
							<th>Работодатель</th>
							<th>Специальность</th>
							<th>Одобренных практикантов</th>
							<th>Статус ответа</th>
							<th>Договор</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Ответов работодателей пока нет - ожидайте утверждения работодателем заявок в целом (специальности + количества).
			</p>
		@endif
	</div>

	<div class="modal fade" id="sign-contract" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
		data-bs-keyboard="false">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Выбор работодателя для регистрации договора</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				</div>
				<div class="modal-body">
					<p>Новый договор включит в себя все специальности работодателя, по которым полностью согласован список практикантов.
					</p>
					<p>
						Если хотите зарегистрировать договор на отдельную специальность - воспользуйтесь меню &laquo;Действия&raquo; для
						согласованной специальности.
					</p>
					<div class="mb-4">
						<select name="employers" class="select2 form-control" style="width:100%;" id="employers"></select>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal-close">Закрыть</button>
					<form method="get" action="{{ route('planning.contracts.create', ['order' => $order, 'answer' => 0]) }}"
						id="register-contract" enctype="multipart/form-data">
						<input type="hidden" name="employer" id="employer" value="">
						<button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Зарегистрировать
							договор с работодателем</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="contracts-list" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
		data-bs-keyboard="false">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Выбор договора</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
				</div>
				<div class="modal-body">
					<div class="mb-4" id="contracts-cue"></div>
					<div class="mb-4">
						<select name="contracts" class="select2 form-control" style="width:100%;" id="contracts"></select>
					</div>
				</div>
				<div class="modal-footer justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal-close">Закрыть</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="link-contract">Добавить специальность в
						договор</button>
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
				function showContracts(answer, response, specialty) {
					const data = [];
					response.contracts.forEach(contract => {
						data.push({
							'id': contract.id,
							'text': contract.text
						})
					})
					data.sort((a, b) => {
						if (a.text === b.text) return 0;
						else if (a.text > b.text) return 1;
						else return -1;
					});
					$('#contracts').empty().select2({
						language: 'ru',
						dropdownParent: $('#contracts-list'),
						data: data,
						// placeholder: 'Выберите одного или нескольких учащихся из выпадающего списка',
						// sorter: function(data) {
						// 	return data.sort(function(a, b) {
						// 		return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
						// 	});
						// }
					})
					document.getElementById('contracts-cue').innerHTML =
						"<p>Выберите договор между образовательным учреждением &laquo;" + response.school + "&raquo; и " +
						"работодателем &laquo;" + response.employer + "&raquo; для добавления в него специальности &laquo;" +
						specialty + "&raquo;"
					document.getElementById('link-contract').dataset.answer = answer;
					let contractsDialog = new bootstrap.Modal(document.getElementById('contracts-list'));
					contractsDialog.show();
				}

				function clickListContracts(answer, school, employer, specialty) {
					$.ajax({
						method: 'POST',
						url: "{{ route('planning.contracts.list') }}",
						data: {
							school: school,
							employer: employer,
						},
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success: (response) => {
							if (response.contracts.length == 0) {
								document.getElementById('alert-title').innerText = "Нет договоров"
								document.getElementById('alert-body').innerHTML =
									"Нет зарегистрированных договоров между образовательным учреждением &laquo;" + response
									.school + "&raquo; и " +
									"работодателем &laquo;" + response.employer +
									"&raquo; для добавления в него специальности &laquo;" + specialty + "&raquo;.<br/>" +
									"Зарегистрируйте новый договор."
								let alertDialog = new bootstrap.Modal(document.getElementById('modal-alert'))
								alertDialog.show()
							} else showContracts(answer, response, specialty)
							// window.datatable.ajax.reload();
						}
					})
				}

				function clickDetach(contract, answer) {
					$.ajax({
						method: 'POST',
						url: "{{ route('planning.contracts.detach') }}",
						data: {
							contract: contract,
							answer: answer,
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
					window.employers = {!! json_encode($ready) !!};
					let select = $('#employers');

					const data = [];
					for (let object in window.employers)
						data.push({
							'id': object,
							'text': window.employers[object]
						});
					data.sort((a, b) => {
						if (a.text === b.text) return 0;
						else if (a.text > b.text) return 1;
						else return -1;
					});
					select.empty().select2({
						language: 'ru',
						dropdownParent: $('#sign-contract'),
						data: data,
						// placeholder: 'Выберите одного или нескольких учащихся из выпадающего списка',
						// sorter: function(data) {
						// 	return data.sort(function(a, b) {
						// 		return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
						// 	});
						// }
					});

					window.datatable = $('#answers_table').DataTable({
						language: {
							"url": "{{ asset('lang/ru/datatables.json') }}"
						},
						processing: true,
						serverSide: true,
						ajax: '{!! route('planning.answers.index.data', ['order' => $order]) !!}',
						responsive: true,
						columns: [{
								data: 'aid',
								name: 'aid',
								responsivePriority: 1
							},
							{
								data: 'employer',
								name: 'employer',
								responsivePriority: 3
							},
							{
								data: 'specialty',
								name: 'specialty',
								responsivePriority: 2
							},
							{
								data: 'approved',
								name: 'approved',
								responsivePriority: 2
							},
							{
								data: 'status',
								name: 'status',
								responsivePriority: 2
							},
							{
								data: 'contract',
								name: 'contract',
								responsivePriority: 3
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

					$('#register-contract').on('submit', (event) => {
						const id = $('#employers').val();
						document.getElementById('employer').value = id;
						// const text = window.employers[id];
					});

					$('#link-contract').on('click', (event) => {
						const answer = event.target.dataset.answer
						const contract = $('#contracts').val()

						$.ajax({
							method: 'POST',
							url: "{{ route('planning.contracts.attach') }}",
							data: {
								answer: answer,
								contract: contract,
							},
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
	@endif
