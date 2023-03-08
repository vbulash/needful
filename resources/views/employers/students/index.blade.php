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
				<p>
					<small>
						Вы можете утвердить всех практикантов или отказаться от всех практикантов по кнопкам ниже.<br />
						Либо утвердить практиканта / отказаться от практиканта в индивидуальном порядке через соответствующее действие
					</small>
				</p>
				<div>
					<button type="button" class="btn btn-primary mt-3 mb-3" id="approve-all">
						Утвердить всех практикантов
					</button>
					<button type="button" class="btn btn-primary mt-3 mb-3" id="reject-all">
						Отказаться от всех практикантов
					</button>
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
@endsection

{{-- @if ($count > 0) --}}
@push('css_after')
	<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
@endpush

@push('js_after')
	<script src="{{ asset('js/datatables.js') }}"></script>
	<script>
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
		});

		document.getElementById('approve-all').onclick = () => {
			clickChangeStatus(0, {{ \App\Models\AnswerStudentStatus::APPROVED->value }});
		}

		document.getElementById('reject-all').onclick = () => {
			clickChangeStatus(0, {{ \App\Models\AnswerStudentStatus::REJECTED->value }});
		}
	</script>
@endpush
{{-- @endif --}}
