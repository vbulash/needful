@extends('layouts.blocks')

@section('steps')
    @php
        $steps = [['title' => 'Входящие сообщения', 'active' => false, 'context' => null], ['title' => 'Архив', 'active' => true, 'context' => null]];
    @endphp
@endsection

@section('blocks')
    <div class="col-12">
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <div class="d-flex">
                    <button class="me-3 btn btn-primary" type="button" id="inbox">&lt; Входящие</button>
                    <button class="btn btn-outline-primary" type="button" disabled>Архив</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-5 col-xl-3">
        {{--		<div class="d-md-none push"> --}}
        {{--			<!-- Class Toggle, functionality initialized in Helpers.dmToggleClass() --> --}}
        {{--			<button type="button" class="btn w-100 btn-alt-primary js-class-toggle-enabled" data-toggle="class-toggle" --}}
        {{--					data-target="#side-content" data-class="d-none"> --}}
        {{--				Список сообщений --}}
        {{--			</button> --}}
        {{--		</div> --}}
        <div id="side-content" class="d-md-block push d-none">
            <div class="list-group fs-sm">
                @if ($tasks->count() == 0)
                    <p>Нет архивных сообщений</p>
                @else
                    <script>
                        let messages = new Map();
                    </script>
                    @php
                        $index = 0;
                    @endphp
                    @foreach ($tasks as $task)
                        <script>
                            messages.set({{ $task->getKey() }}, {
                                title: "{{ $task->title }}",
                                description: `{!! $task->description !!}`,
                                read: {{ $task->read }},
                                route: "{{ $task->route }}",
                                from: "{{ $task->fromadmin ? 'Администратор платформы' : $task->from->name }}",
                                to: "{{ $task->toadmin ? 'Администратор платформы' : $task->to->name }}",
                                timestamp: "{{ $task->created_at->format('d.m.Y G:i:s') }}",
                            });
                        </script>
                        @php
                            if ($index == 0) {
                                $titleClassList = 'list-group-item active list-group-item-action';
                                $digestClassList = 'text-white mb-2';
                                $fromClassList = 'fs-sm text-white mb-0';
                            } else {
                                $titleClassList = 'list-group-item list-group-item-action';
                                $digestClassList = 'text-muted mb-2';
                                $fromClassList = 'fs-sm text-muted mb-0';
                            }
                            if (!$task->read) {
                                $titleClassList .= ' unread';
                            }
                        @endphp
                        <a class="{{ $titleClassList }}" data-bs-toggle="list" role="tab" href="javascript:void(0)"
                            data-id="{{ $task->getKey() }}" id="message-{{ $task->getKey() }}">
                            <p class="fs-6 fw-bold mb-2">
                                {{ $task->title }}
                            </p>
                            <p class="{{ $digestClassList }}" id="description-{{ $task->getKey() }}">
                                {!! Str::of(strip_tags($task->description))->limit(96) !!}
                            </p>
                            <p class="{{ $fromClassList }}">
                                <strong><span
                                        id="sender-{{ $task->getKey() }}">{{ $task->fromadmin ? 'Администратор платформы' : $task->from->name }}</span></strong>,
                                <small><span
                                        id="timestamp-{{ $task->getKey() }}">{{ $task->created_at->format('d.m.Y G:i:s') }}</span></small>
                            </p>
                        </a>
                        @php
                            $index++;
                        @endphp
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @php
        $task = $tasks->first();
    @endphp
    @if ($tasks->count() != 0)
        <script>
            window.message = {{ $task->getKey() }};
        </script>
        <div class="col-md-8 col-lg-7 col-xl-9">
            <div class="">
                <div class="block block-rounded sticky-top">
                    <div class="block-header block-header-default">
                        <div class="col-sm-5 d-sm-flex align-items-sm-center">
                            <div class="fw-bold text-muted text-sm-start w-100 mt-2 mt-sm-0">
                                <p class="mb-0" id="card-address">
                                    {{ $task->fromadmin ? 'Администратор платформы' : $task->from->name }}
                                    -> {{ $task->toadmin ? 'Администратор платформы' : $task->to->name }}</p>
                                <p class="mb-0"><span id="card-title">{{ $task->title }}</span></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-sm btn-primary me-2 mb-2" id="card-restore">Восстановить из
                                архива</button>
                            <button type="button" class="btn btn-sm btn-primary me-2 mb-2" id="card-delete">Удалить</button>
                        </div>
                        <div class="col-sm-3 d-sm-flex align-items-sm-center">
                            <div class="fs-sm text-muted text-sm-end w-100 mt-2 mt-sm-0">
                                <p class="mb-0"><span
                                        id="card-timestamp">{{ $task->created_at->format('d.m.Y G:i:s') }}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="block-content" id="card">
                        <span id="card-description">{!! $task->description !!}</span>
                        <p class="mt-4">Ссылка на объект сообщения: <span id="card-route">{{ $task->route }}</span></p>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.querySelectorAll('#card *').forEach(element => {
                element.setAttribute('disabled', 'disabled');
            });
        </script>
    @endif
@endsection

@push('js_after')
    <script>
        function drawCard() {
            let card = {
                id: window.message,
                list: 'list-group-item active list-group-item-action message',
                digest: 'text-white mb-2',
                from: 'fs-sm text-white mb-0'
            };
            const current = messages.get(parseInt(card.id));

            document.getElementById('message-' + card.id).setAttribute('class', card.list);
            document.getElementById('description-' + card.id).setAttribute('class', card.digest);
            document.getElementById('sender-' + card.id).parentElement.parentElement.setAttribute('class', card.from);

            // Перечитать карточку деталей сообщения
            document.getElementById('card-title').innerText = current.title;
            document.getElementById('card-address').innerText = current.from + ' > ' + current.to;
            document.getElementById('card-description').innerHTML = current.description;
            document.getElementById('card-timestamp').innerText = current.timestamp;
            document.getElementById('card-route').setAttribute('href', current.route === null ? 'javascript:void(0)' :
                current.route)
            document.getElementById('card-route').innerText = current.route === null ? 'javascript:void(0)' : current.route;

            document.querySelectorAll('#card *').forEach(element => {
                element.setAttribute('disabled', 'disabled');
            });
        }

        const tabElms = document.querySelectorAll('a[data-bs-toggle="list"]')
        if (tabElms)
            tabElms.forEach(tabElm => {
                tabElm.addEventListener('hidden.bs.tab', event => {
                    let card = {
                        id: event.target.dataset.id,
                        list: 'list-group-item list-group-item-action message',
                        digest: 'text-muted mb-2',
                        from: 'fs-sm text-muted mb-0'
                    };
                    const current = messages.get(parseInt(card.id));
                    if (!current.read)
                        card.list = card.list + ' unread';

                    document.getElementById('message-' + card.id).setAttribute('class', card.list);
                    document.getElementById('description-' + card.id).setAttribute('class', card.digest);
                    document.getElementById('sender-' + card.id).parentElement.parentElement.setAttribute(
                        'class', card.from);
                }, false);

                tabElm.addEventListener('shown.bs.tab', event => {
                    window.message = event.target.dataset.id;
                    drawCard();
                }, false);
            })

        document.getElementById('inbox').addEventListener('click', event => {
            window.location.href = '{{ route('inbox.index') }}';
        }, false);

        const cardRestore = document.getElementById('card-restore');
        if (cardRestore)
            cardRestore.addEventListener('click', event => {
                $.ajax({
                    method: 'GET',
                    url: "{{ route('message.archive') }}",
                    data: {
                        message: window.message,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: () => {
                        window.location.href = "{{ route('inbox.index') }}";
                    }
                });
            }, false);

        document.addEventListener("DOMContentLoaded", () => {
            //read.dispatchEvent(new Event('change'));
        }, false);
    </script>
@endpush
