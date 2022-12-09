@extends('layouts.chain')

@section('service')
    @if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value))
        Мои практики
    @else
        Работа с практиками
    @endif
@endsection

@section('steps')
    @php
        $steps = [['title' => 'Практика', 'active' => false, 'context' => 'history', 'link' => route('history.index', ['sid' => session()->getId()])], ['title' => 'Практиканты', 'active' => true, 'context' => 'trainee']];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        @if (!auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value) &&
            $history->status != \App\Models\HistoryStatus::DESTROYED->value)
            <div>
                <button type="button" class="btn btn-primary mt-3 mb-3" id="add-trainees" data-bs-toggle="modal"
                    data-bs-target="#trainee-list">
                    Привязать учащихся<br />к практике
                </button>
                <button type="button" class="btn btn-primary ms-2 mt-3 mb-3" id="mail-new">Разослать приглашения<br />новым
                    практикантам
                </button>
            </div>
        @endif
    </div>
    <div class="block-content p-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap" id="trainees_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 30px">#</th>
                        <th>ФИО практиканта</th>
                        <th>Электронная почта практиканта</th>
                        <th>Статус практиканта</th>
                        <th>Действия</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="trainee-list" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Выбор учащихся для практики</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start">
                        <div class="form-control mb-4" id="no-students" style="display: none;">
                            <p>Нет учащихся в активном статусе</p>
                        </div>
                        <select name="tests" class="select2 form-control" style="width:100%;" id="students">
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="modal-close">Закрыть
                    </button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" data-role="submit"
                        id="link-trainees" disabled>Привязать к практике
                    </button>
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
        function clickTransition(button) {
            $.ajax({
                method: 'POST',
                url: "{{ route('trainees.transition') }}",
                data: {
                    history: button.dataset.history,
                    student: button.dataset.student,
                    from: button.dataset.from,
                    to: button.dataset.to,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: () => {
                    window.datatable.ajax.reload();
                }
            });
        }

        function reloadSelect(data) {
            if (data.data.length === 0) {
                document.getElementById('add-trainees').disabled = true;
            } else {
                document.getElementById('add-trainees').disabled = false;
                let select = $('#students');
                select.empty().select2({
                    language: 'ru',
                    dropdownParent: $('#trainee-list'),
                    data: data.data,
                    templateResult: formatRecord,
                    multiple: true,
                    placeholder: 'Выберите одного или нескольких учащихся из выпадающего списка',
                });
				select.trigger('change');
            }
            document.getElementById('mail-new').disabled = data.new === 0;
        }

        $('#students').on('select2:select', (event) => {
            // let data = event.params.data;
            $('#link-trainees').removeAttr('disabled');
        });

        $('#students').on('select2:unselect', (event) => {
            // let data = event.params.data;
            if ($('#students').val().length === 0)
                $('#link-trainees').attr('disabled', true);
            else
                $('#link-trainees').removeAttr('disabled');
        });

        document.getElementById('link-trainees').addEventListener('click', (event) => {
            $.ajax({
                method: 'POST',
                url: "{{ route('trainees.link') }}",
                data: {
                    trainees: $('#students').val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (data) => {
                    if (data !== undefined) {
                        data = JSON.parse(data);
                        reloadSelect(data);
                        window.datatable.ajax.reload();
                    }
                    $('#students').val(null).trigger('change');
                }
            });
        }, false);

        const mailNew = document.getElementById('mail-new');
        if (mailNew)
            document.getElementById('mail-new').addEventListener('click', (event) => {
                $.ajax({
                    method: 'POST',
                    url: "{{ route('trainees.invite.all') }}",
                    data: {
                        all: false
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: (data) => {
                        document.getElementById('mail-new').disabled = true;
                        window.datatable.ajax.reload();
                    }
                });
            }, false);

        document.getElementById('confirm-yes').addEventListener('click', (event) => {
            $.ajax({
                method: 'POST',
                url: "{{ route('trainees.unlink') }}",
                data: {
                    trainee: event.target.dataset.id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (data) => {
                    if (data !== undefined) {
                        data = JSON.parse(data);
                        reloadSelect(data);
                        window.datatable.ajax.reload();
                    }
                }
            });
        }, false);

        function clickUnlink(stud_id, trainee_id) {
            document.getElementById('confirm-title').innerText = "Подтвердите удаление";
            document.getElementById('confirm-body').innerHTML = "Удалить запись привязки практиканта № " + trainee_id +
            " ?";
            document.getElementById('confirm-yes').dataset.id = stud_id;
            let confirmDialog = new bootstrap.Modal(document.getElementById('modal-confirm'));
            confirmDialog.show();
        }

        function formatRecord(record) {
            if (!record.id) return record.text;

            if (isNaN(parseInt(record.id))) return record.text;

            return $(
                "<div class='row'>\n" +
                "<div class='ml-2 col-7'>" + record.text + "</div>\n" +
                "<div class='col-2'>" + record.birthdate + "</div>\n" +
                "<div class='col-2 mr-2'>" + record.phone + "</div>\n" +
                "</div>\n"
            );
        }

        $(function() {
            window.datatable = $('#trainees_table').DataTable({
                language: {
                    "url": "{{ asset('lang/ru/datatables.json') }}"
                },
                processing: true,
                serverSide: true,
                ajax: '{!! route('trainees.index.data') !!}',
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
                        data: 'fio',
                        name: 'fio',
                        responsivePriority: 2
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

            @if ($history->status != \App\Models\HistoryStatus::DESTROYED->value)
                let data = {!! $students !!};
                reloadSelect(data);
            @endif
        });
    </script>
@endpush
