@extends('layouts.chain')

@section('service')
    @if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value))
        Мои стажировки
    @else
        Работа со стажировками
    @endif
@endsection

@section('steps')
    @php
        $steps = [['title' => 'Стажировки', 'active' => true, 'context' => 'history'], ['title' => 'Практиканты', 'active' => false, 'context' => 'trainee']];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        <div>
            <span>Новая история стажировки создается через услугу на <a
                    href="{{ route('dashboard', ['sid' => session()->getId()]) }}">главной странице</a></span><br/>
            <span><small>В поле &laquo;Практиканты&raquo; отображается общее количество утвержденных практикантов из общего
                    числа запланированных</small></span><br/><br/>
			<span>После набора планового количества практикантов не забудьте назначить стажировке статус
				&laquo;{{ \App\Models\HistoryStatus::getName(\App\Models\HistoryStatus::PLANNED->value) }}&raquo;</span>
        </div>
    </div>
    <div class="block-content p-4">
        @if ($count > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap" id="histories_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 30px">#</th>
                            <th>Работодатель</th>
                            <th>Стажировка</th>
                            <th>График стажировки</th>
                            <th>Практиканты</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @else
            <p>Записей стажировок пока нет...</p>
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
            document.getElementById('confirm-yes').addEventListener('click', (event) => {
                const confirmType = document.getElementById('confirm-type').value;
                switch (confirmType) {
                    case 'delete':
                        $.ajax({
                            method: 'DELETE',
                            url: "{{ route('history.destroy', ['history' => '0']) }}",
                            data: {
                                id: event.target.dataset.id,
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: () => {
                                window.datatable.ajax.reload();
                            }
                        });
                        break;
                    case 'cancel':
                        $.ajax({
                            method: 'POST',
                            url: "{{ route('history.cancel') }}",
                            data: {
                                history: event.target.dataset.id,
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: () => {
                                window.datatable.ajax.reload();
                            }
                        });
                        break;
                }
            }, false);

            function clickDelete(id) {
                $.ajax({
                    method: 'POST',
                    url: "{{ route('history.can.destroy') }}",
                    data: {
                        history: id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: (can) => {
                        if (can === "true") {
                            document.getElementById('confirm-title').innerText = "Подтвердите удаление";
                            document.getElementById('confirm-body').innerHTML =
                                "Удалить запись истории стажировки № " + id + " ?";
                            document.getElementById('confirm-yes').dataset.id = id;
                            document.getElementById('confirm-type').value = 'delete';
                        } else if (can === "false") {
                            document.getElementById('confirm-title').innerText = "Подтвердите отмену стажировки";
                            document.getElementById('confirm-body').innerHTML =
                                "<p>Удалить запись истории стажировки № " + id +
                                " нельзя, поскольку уже рассылались приглашения учащимся.</p>" +
                                "<p>Такую стажировку можно только отменить, разослав извинительные письма всем участникам.</p>" +
                                "<p>Отменить стажировку?</p>";
                            document.getElementById('confirm-yes').dataset.id = id;
                            document.getElementById('confirm-type').value = 'cancel';
                        }
                        let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
                        confirmDialog.show();
                    }
                });
            }

            $(function() {
                window.datatable = $('#histories_table').DataTable({
                    language: {
                        "url": "{{ asset('lang/ru/datatables.json') }}"
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! route('history.index.data') !!}',
                    responsive: true,
                    columns: [{
                            data: 'id',
                            name: 'id',
                            responsivePriority: 1
                        },
                        {
                            data: 'employer',
                            name: 'employer',
                            responsivePriority: 1
                        },
                        {
                            data: 'internship',
                            name: 'internship',
                            responsivePriority: 3
                        },
                        {
                            data: 'timetable',
                            name: 'timetable',
                            responsivePriority: 2
                        },
                        {
                            data: 'trainees',
                            name: 'trainees',
                            responsivePriority: 2
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
            });
        </script>
    @endpush
@endif
