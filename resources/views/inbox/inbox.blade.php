@extends('layouts.blocks')

@section('steps')
	@php
		$steps = [
			['title' => 'Входящие сообщения', 'active' => true, 'context' => null],
		];
	@endphp
@endsection

@section('blocks')
	<div class="col-md-4 col-lg-5 col-xl-3">
		{{--		<div class="d-md-none push">--}}
		{{--			<!-- Class Toggle, functionality initialized in Helpers.dmToggleClass() -->--}}
		{{--			<button type="button" class="btn w-100 btn-alt-primary js-class-toggle-enabled" data-toggle="class-toggle"--}}
		{{--					data-target="#side-content" data-class="d-none">--}}
		{{--				Список сообщений--}}
		{{--			</button>--}}
		{{--		</div>--}}
		<div id="side-content" class="d-md-block push d-none">
			<div class="list-group fs-sm">
				<script>
					let messages = new Map();
				</script>
				@php
					$index = 0;
				@endphp
				@foreach($tasks as $task)
					<script>
						messages.set({{ $task->getKey() }}, {
							title: "{{ $task->title }}",
							description: `{!! $task->description !!}`,
							read: {{ $task->read }},
							route: "{{ $task->route }}",
							from: "{{ $task->fromadmin ? 'Администратор платформы' : $task->from->name }}",
							to: "{{ $task->toadmin ? 'Администратор платформы' : $task->to->name }}",
							@if (isset($task->context))
								context: {!! $task->context !!},
							@else
								context: '',
							@endif
							timestamp: "{{ $task->created_at->format('d.m.Y G:i:s') }}",
							@if (isset($task->script))
								script: `{!! $task->script !!}`,
							@else
								script: '',
							@endif
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
					   data-id="{{ $task->getKey() }}"
					   id="message-{{ $task->getKey() }}">
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
			</div>
		</div>
	</div>

	@php
		$task = $tasks->first();
	@endphp
	<script>window.message = {{ $task->getKey() }};</script>
	<div class="col-md-8 col-lg-7 col-xl-9">
		<div class="">
			<div class="block block-rounded sticky-top">
				<div class="block-header block-header-default">
					<div class="col-sm-6 d-sm-flex align-items-sm-center">
						<div class="fw-bold text-muted text-sm-start w-100 mt-2 mt-sm-0">
							<p class="mb-0"
							   id="card-address">{{ $task->fromadmin ? 'Администратор платформы' : $task->from->name }}
								-> {{ $task->toadmin ? 'Администратор платформы' : $task->to->name }}</p>
							<p class="mb-0"><span id="card-title">{{ $task->title }}</span></p>
						</div>
					</div>
					<div class="col-sm-3">
						<button type="button" class="btn btn-sm btn-primary" id="card-read">
							@if ($task->read)
								Отметить как непрочтённое
							@else
								Отметить как прочтённое
							@endif
						</button>
					</div>
					<div class="col-sm-3 d-sm-flex align-items-sm-center">
						<div class="fs-sm text-muted text-sm-end w-100 mt-2 mt-sm-0">
							<p class="mb-0"><span
									id="card-timestamp">{{ $task->created_at->format('d.m.Y G:i:s') }}</span></p>
						</div>
					</div>
				</div>
				<div class="block-content">
					<span id="card-description">{!! $task->description !!}</span>
					<p class="mt-4">Ссылка на объект сообщения:
						<a id="card-route" href="{{ $task->route }}" data-id="{{ $task->getKey() }}">{{ $task->route }}</a>
					</p>
				</div>
			</div>
		</div>
	</div>
	<script id="script">{!! $task->script ?? 'nbsp;' !!}</script>
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
			if (!current.read)
				card.list = card.list + ' unread';

			document.getElementById('message-' + card.id).setAttribute('class', card.list);
			document.getElementById('description-' + card.id).setAttribute('class', card.digest);
			document.getElementById('sender-' + card.id).parentElement.parentElement.setAttribute('class', card.from);

			// Перечитать карточку деталей сообщения
			document.getElementById('card-title').innerText = current.title;
			document.getElementById('card-address').innerText = current.from + ' > ' + current.to;
			document.getElementById('card-description').innerHTML = current.description;
			document.getElementById('card-timestamp').innerText = current.timestamp;
			document.getElementById('card-route').setAttribute('href', current.route === null ? 'javascript:void(0)' : current.route)
			document.getElementById('card-route').innerText = current.route === null ? 'javascript:void(0)' : current.route;

			if (current.read)
				document.getElementById('card-read').innerText = 'Отметить как непрочтённое';
			else
				document.getElementById('card-read').innerText = 'Отметить как прочтённое';

			document.getElementById('script').innerText = current.route === null ? '' : current.script;
		}

		const tabElms = document.querySelectorAll('a[data-bs-toggle="list"]')
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
				document.getElementById('sender-' + card.id).parentElement.parentElement.setAttribute('class', card.from);
			}, false);

			tabElm.addEventListener('shown.bs.tab', event => {
				window.message = event.target.dataset.id;
				drawCard();
			}, false);
		})

		document.getElementById('card-route').addEventListener('click', event => {
			const id = event.target.dataset.id;
			const message = messages.get(parseInt(id));

			if (!message.context) return;

			event.preventDefault();
			event.stopPropagation();

			$.ajax({
				method: 'GET',
				url: "{{ route('message.link') }}",
				data: {
					context: JSON.stringify(message.context),
					route: message.route
				},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: url => {
					window.location.href = url;
				}
			});
		}, false);

		document.getElementById('card-read').addEventListener('click', event => {
			$.ajax({
				method: 'POST',
				url: "{{ route('message.read') }}",
				data: {
					message: window.message,
				},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				success: () => {
					$before = messages.get(parseInt(window.message));
					$before.read = !$before.read;
					messages.set(parseInt(window.message), $before);

					if ($before.read)
						document.getElementById('card-read').innerText = 'Отметить как непрочтённое';
					else
						document.getElementById('card-read').innerText = 'Отметить как прочтённое';
				}
			});
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			//read.dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
