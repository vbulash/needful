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
			<h3 class="block-title fw-semibold mb-4">Практиканты от образовательного учреждения
				&laquo;{{ $school->getTitle() }}&raquo; для работодателя &laquo;{{ $employer->getTitle() }}&raquo;</h3>
			@if ($answer->status == App\Models\AnswerStatus::DONE->value)
				<p>Статус &laquo;{{ App\Models\AnswerStatus::getName($answer->status) }}&raquo; конечный - более никакие действия над
					списком практикантов невозможны. Время заключать
					договор!</p>
			@else
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
					<button type="button" class="btn btn-primary mt-3 mb-3 me-4" id="send-students" disabled>
						Уведомить работодателя
					</button>
					<button type="button" class="btn btn-primary mt-3 mb-3" id="fix-students" disabled>
						Зафиксировать практикантов
					</button>
				</div>
				<p>
					<small>
						Пока в списке практикантов остается хоть один
						отклоненный работодателем или зарезервированный по другой заявке, кнопка &laquo;Уведомить работодателя&raquo;
						останется недоступной.<br />
						Если все практиканты в списке станут одобренными (не более {{ $answer->approved }}), вы сможете окончательно
						зафиксировать
						практикантов в заявке по специальности
						&laquo;{{ $specialty->name }}&raquo; по работодателю &laquo;{{ $employer->getTitle() }}&raquo;.
					</small>
				</p>
			@endif
		</div>
	</div>
	<div class="block-content p-4">
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

	<div class="modal fade" id="modal-send" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
		aria-labelledby="modal-answer-label" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<form action="" method="post" id="form-answer" enctype="multipart/form-data">
				@csrf
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="answer-title">Образовательное учреждение приняло решение по всем практикантам</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body" id="answer-body">
						<p class="mb-4">Вы можете добавить в сообщение работодателю необязательную дополнительную
							информацию из поля
							ниже:</p>
						<div class="form-floating mb-4">
							<textarea class="form-control" id="message" name="message" placeholder="Сообщение" style="height: 200px;"></textarea>
							<label class="form-label" for="message">Дополнительная информация &gt;</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Уведомить работодателя</button>
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
					</div>
				</div>
			</form>
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
		const addStudent = document.getElementById('add-student');
		const noData = document.getElementById('no-enabled-data');

		function reloadSelect() {
			if (Object.keys(window.enabled).length === 0) {
				if (addStudent)
					addStudent.style.display = 'none';
				if (noData)
					noData.style.display = 'block';
			} else {
				if (addStudent)
					addStudent.style.display = 'block';
				let select = $('#students');

				const data = [];
				for (let index in window.enabled)
					data.push({
						'id': index,
						'text': window.enabled[index].text,
						'status': window.enabled[index].status,
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
				if (noData)
					noData.style.display = 'none';
			}

			// Подсчитать количества
			let countAll = 0;
			let countNew = 0;
			let countInvited = 0;
			let countRejected = 0;
			let countApproved = 0;
			let countReserved = 0;
			for (let index in window.selected) {
				countAll++;
				switch (window.selected[index].status) {
					case {{ App\Models\AnswerStudentStatus::NEW->value }}:
						countNew++;
						break;
					case {{ App\Models\AnswerStudentStatus::INVITED->value }}:
						countInvited++;
						break;
					case {{ App\Models\AnswerStudentStatus::REJECTED->value }}:
						countRejected++;
						break;
					case {{ App\Models\AnswerStudentStatus::APPROVED->value }}:
						countApproved++;
						break;
					case {{ App\Models\AnswerStudentStatus::RESERVED->value }}:
						countReserved++;
						break;
				}
			}
			const goal = {{ $answer->approved }};
			if (
				(countRejected != 0) ||
				(countReserved != 0) ||
				(countNew + countInvited + countRejected + countApproved > goal) ||
				(countNew + countInvited + countRejected + countApproved == 0)
			) {
				document.getElementById('send-students').disabled = true;
				document.getElementById('fix-students').disabled = true;
			} else {
				if (
					(countApproved == countAll) ||
					(countNew = countAll)
				) {
					document.getElementById('send-students').disabled = false;
				}

				if (countApproved == countAll) {
					document.getElementById('fix-students').disabled = false;
				}
			}
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
					const text = window.selected[id].text;
					const status = window.selected[id].status;
					delete window.selected[id];
					window.enabled[id] = {
						text: text,
						status: status
					};

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
					if (data.status ===
						"{{ App\Models\AnswerStudentStatus::getName(App\Models\AnswerStudentStatus::REJECTED->value) }}"
					) {
						row.style.color = 'red';
					}
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
				const text = window.enabled[id].text;

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
						window.selected[id] = {
							text: text,
							status: {{ App\Models\AnswerStudentStatus::NEW->value }}
						};

						reloadSelect();
						window.datatable.ajax.reload();
					}
				});
			});

			$('#send-students').on('click', (event) => {
				let answerDialog = new bootstrap.Modal(document.getElementById('modal-send'));
				answerDialog.show();
			});

			$('#form-answer').submit(event => {
				event.preventDefault();
				$.ajax({
					method: 'POST',
					url: "{{ route('planning.students.send', ['answer' => $answer->getKey()]) }}",
					data: {
						message: $('#message').val(),
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						reloadSelect();
						window.datatable.ajax.reload();
					}
				});
			});

			$('#fix-students').on('click', (event) => {
				$.ajax({
					method: 'GET',
					url: "{{ route('planning.students.fix', ['answer' => $answer->getKey()]) }}",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: () => {
						// reloadSelect();
						window.location.reload();
					}
				});
			});
		});
	</script>
@endpush
{{-- @endif --}}
