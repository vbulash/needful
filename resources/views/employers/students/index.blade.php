@extends('layouts.chain')

@section('service')
	Работа с работодателями
@endsection

@section('steps')
	@php
		$context = session('context');
		$order = \App\Models\Order::find($context['order']);
		$school = $order->school;
		$answer = \App\Models\Answer::find($context['answer']);
		$employer = $answer->employer;
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
		        'active' => false,
		        'context' => 'answer',
		        'link' => route('employers.orders.answers.index', ['employer' => $employer->getKey(), 'order' => $order->getKey()]),
		    ],
		    [
		        'title' => 'Предложенные практиканты',
		        'active' => true,
		        'context' => 'employer.students',
		    ],
		];
	@endphp
@endsection

@section('interior')
	<div class="block-header block-header-default">
		<div>
			<h3 class="block-title fw-semibold mb-4">Практиканты, предложенные образовательным учреждением</h3>
			@if ($count > 0)
				<div>
					<small>
						Вы можете утвердить всех практикантов или отказаться от всех практикантов по кнопкам ниже.<br />
						Либо утвердить практиканта / отказаться от практиканта в индивидуальном порядке через соответствующее действие
					</small>
				</div>
				<div>
					<button type="button" class="btn btn-primary mt-3 mb-3" id="approve-all">
						Утвердить всех практикантов
					</button>
					<button type="button" class="btn btn-primary mt-3 mb-3" id="reject-all">
						Отказаться от всех практикантов
					</button>
				</div>
				<button type="button" class="btn btn-primary mt-3 mb-3" id="send-students">
					Уведомить образовательное учреждение
				</button>
				<div>
					<small>
						После того как вы вынесете решение по всем практикантам, предложенным образовательным учреждением (одобрите или
						отклоните), у вас появится возможность уведомить образовательное учреждение о принятом решении.
					</small>
				</div>
			@endif
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
			<p>Практикантов пока нет.<br />Ожидайте наполнение заявки реальными практикантами после вашего утверждения заявки в
				целом
				(специальности + количества).</p>
		@endif
	</div>

	<div class="modal fade" id="modal-answer" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
		aria-labelledby="modal-answer-label" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<form action="" method="post" id="form-answer" enctype="multipart/form-data">
				@csrf
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="answer-title">Работодатель принял решение по всем предложенным практикантам</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body" id="answer-body">
						<p class="mb-4">Вы можете добавить в сообщение образовательному учреждению необязательную дополнительную
							информацию из поля
							ниже:</p>
						<div class="form-floating mb-4">
							<textarea class="form-control" id="message" name="message" placeholder="Сообщение" style="height: 200px;"></textarea>
							<label class="form-label" for="message">Дополнительная информация &gt;</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" id="answer-yes" data-bs-dismiss="modal">Уведомить образовательное
							учреждение</button>
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
		const goal = {{ $count }};

		function checkSendButton() {
			let countRejected = 0;
			let countApproved = 0;
			for (let index in window.selected)
				switch (window.selected[index]) {
					case {{ App\Models\AnswerStudentStatus::REJECTED->value }}:
						countRejected++;
						break;
					case {{ App\Models\AnswerStudentStatus::APPROVED->value }}:
						countApproved++;
						break;
				}
			document.getElementById('send-students').disabled = !(
				// Допустимые варианты для видимости кнопки
				countApproved == goal || // Все практиканты одобрены
				countRejected == goal || // Все практиканты отклонены
				countApproved + countRejected == goal // Все практиканты разобраны (одобрены или отклонены)
			);
		}

		function clickChangeStatus(student, status) {
			$.ajax({
				method: 'POST',
				url: '{{ route('employers.students.status') }}',
				data: {
					status: status,
					student: student,
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: () => {
					if (student == 0) {
						//
					} else {
						window.selected[student] = status;
					}
					checkSendButton();
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
				ajax: '{!! route('employers.students.index.data') !!}',
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
			checkSendButton();
		});

		document.getElementById('approve-all').onclick = () => {
			clickChangeStatus(0, {{ \App\Models\AnswerStudentStatus::APPROVED->value }});
		}

		document.getElementById('reject-all').onclick = () => {
			clickChangeStatus(0, {{ \App\Models\AnswerStudentStatus::REJECTED->value }});
		}

		document.getElementById('send-students').onclick = () => {
			let answerDialog = new bootstrap.Modal(document.getElementById('modal-answer'));
			answerDialog.show();
		}
	</script>
@endpush
{{-- @endif --}}
