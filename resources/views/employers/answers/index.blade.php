@extends('layouts.chain')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Работодатель',
		        'active' => false,
		        'context' => 'employer',
		        'link' => route('employers.index'),
		    ],
		    [
		        'title' => 'Заявки на практику',
		        'active' => false,
		        'context' => 'order',
		        'link' => route('employers.orders.index', compact('employer')),
		    ],
		    [
		        'title' => 'Ответы на заявку',
		        'active' => true,
		        'context' => 'answer',
		    ],
		    [
		        'title' => 'Выбор практикантов',
		        'active' => false,
		        'context' => 'employer.students',
		    ],
		];
		$_employer = App\Models\Employer::findOrFail($employer);
		$_order = $_employer->orders()->find($order);
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<h3 class="block-title fw-semibold">Ответы на заявку на практику &laquo;{{ $_order->getTitle() }}&raquo;</h3>
			<p class='mb-4'>Статус заявки: {{ App\Models\OrderEmployerStatus::getName($_order->pivot->status) }}</p>
			@if (
				$_order->pivot->status != App\Models\OrderEmployerStatus::ACCEPTED->value &&
					$_order->pivot->status != App\Models\OrderEmployerStatus::REJECTED->value)
				<a onclick="clickReject()" class="btn btn-primary mt-3 mb-3">Отказать учебному заведению</a>
				<a onclick="clickAccept()" class="btn btn-primary mt-3 mb-3">Принять заявку учебного заведения</a>
				<p><small>Полная приёмка заявки - без корректировки ответов в таблице ниже<br />
						Частичная приёмка заявки - с корректировкой поля &laquo;Согласны принять&raquo; в таблице ниже
					</small></p>
			@else
				<p><small>
						Статус заявки &laquo;{{ App\Models\OrderEmployerStatus::getName($_order->pivot->status) }}&raquo; конечный - ответ
						(количество практикантов, которое вы согласны принять) уже нельзя отредактировать
					</small></p>
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
							<th>Название специальности</th>
							<th>Запрос практикантов</th>
							<th>Согласны принять</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
		@else
			<p>Ответов на заявку на практику для текущего работодателя пока нет...</p>
		@endif
	</div>

	<div class="modal fade" id="modal-answer" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
		aria-labelledby="modal-answer-label" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<form action="" id="form-answer">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="answer-title">&nbsp;</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body" id="answer-body">
						<p class="mb-4">Вы можете добавить в сообщение работодателю необязательную дополнительную информацию из поля
							ниже:</p>
						<div class="form-floating mb-4">
							<textarea class="form-control" id="message" name="message" placeholder="Сообщение" style="height: 200px;" required></textarea>
							<label class="form-label" for="message">Дополнительная информация &gt;</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" id="answer-yes" data-bs-dismiss="modal">Отправить</button>
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
					</div>
				</div>
			</form>
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
			function clickAccept() {
				document.getElementById('answer-title').innerText = "Принять заявку от образовательного учреждения";
				document.getElementById('answer-yes').innerText = 'Принять заявку';
				document.getElementById("form-answer").action =
					"{{ route('employers.orders.accept', compact('employer', 'order')) }}";
				let answerDialog = new bootstrap.Modal(document.getElementById('modal-answer'));
				answerDialog.show();
			}

			function clickReject() {
				document.getElementById('answer-title').innerText = "Отказаться от заявки образовательного учреждения";
				document.getElementById('answer-yes').innerText = 'Отказаться от заявки';
				document.getElementById("form-answer").action =
					"{{ route('employers.orders.reject', compact('employer', 'order')) }}";
				let answerDialog = new bootstrap.Modal(document.getElementById('modal-answer'));
				answerDialog.show();
			}

			document.getElementById('answer-yes').addEventListener('click', (event) => {
				let url = '';
				const mode = document.getElementById('answer-mode').value;
				if (mode == 'accept') url = "{{ route('employers.orders.accept', compact('employer', 'order')) }}";
				else if (mode == 'reject') url = "{{ route('employers.orders.reject', compact('employer', 'order')) }}";
				$.ajax({
					method: 'GET',
					url: url,
					data: {
						message: document.getElementById('message').value,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						window.datatable.ajax.reload();
					}
				});
			}, false);

			$(function() {
				window.datatable = $('#answers_table').DataTable({
					language: {
						"url": "{{ asset('lang/ru/datatables.json') }}"
					},
					processing: true,
					serverSide: true,
					ajax: '{!! route('employers.orders.answers.index.data', ['employer' => $employer, 'order' => $order]) !!}',
					responsive: true,
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
							data: 'quantity',
							name: 'quantity',
							responsivePriority: 1
						},
						{
							data: 'approved',
							name: 'approved',
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
			});
		</script>
	@endpush
@endif
