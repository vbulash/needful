@extends('layouts.chain')

@section('steps')
	@php
		$steps = [['title' => 'Импорт учащихся', 'active' => true, 'context' => 'null']];
	@endphp
@endsection

@php
	$index = 1;
@endphp
@section('interior')
	<div class="block-header block-header-default">
		<div>
			<form action="{{ route('import.download') }}" method="GET">
				@csrf
				<p>Здесь вы можете скачать таблицу-шаблон для заполнения данными учащихся:</p>
				<button type="submit" class="btn btn-secondary mb-4">Скачать шаблон для заполнения</button>
			</form>
			<form action="{{ route('import.download.specialties') }}" method="GET">
				@csrf
				<p>В заполнении таблицы импорта учащихся поможет справочник специальностей:</p>
				<button type="submit" class="btn btn-secondary">Экспортировать и скачать справочник специальностей</button>
			</form>
			<p></p>
		</div>
	</div>

	<form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data">
		@csrf
		<div class="block-content p-4">
			<div>
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="school">{{ $index++ }}. Выберите учебное заведение, к
						которому будет привязаны
						импортированные учащиеся
					</label>
					<div class="col-sm-5">
						<select class="form-control select2" name="school" id="school">
							@foreach ($schools as $key => $value)
								<option value="{{ $key }}" @if ($key == $school) selected @endif>
									{{ $value }}
								</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label" for="school">{{ $index++ }}. Выберите файл таблицы для
						импорта
					</label>
					<div class="col-sm-8">
						<input type="file" name="upload" id="upload" accept=".xls, .xlsx">
					</div>
				</div>
				<div class="row mb-4">
					<div class="col-sm-8">
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox" id="rewrite" name="rewrite">
							<label class="form-check-label" for="rewrite">{{ $index }}. Выбран режим &laquo;НЕ
								пересоздавать учащегося при совпадении электронной почты&raquo;</label>
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-primary mb-4" id="btn-import">Импортировать учащихся</button>
				<p>
					<small>
						Обращаем ваше внимание: при импорте учащихся будет происходить попытка создания пользователя с
						адресом
						электронной почты, указанной в строке таблицы. Если пользователь с такой электронной почтой уже
						существует, то будет происходить привязка импортированного учащегося к данному пользователю. Для
						новых
						пользователей будет создаваться случайно сгенерированный пароль, который пользователь не узнает, но
						сможет восстановить через функцию &laquo;Забыли пароль&raquo; на диалоге входа в платформу
						&laquo;{{ env('APP_NAME') }}&raquo;
					</small>
				</p>
			</div>
		</div>
	</form>
@endsection

@push('js_after')
	<script>
		let rewrite = document.getElementById('rewrite');
		rewrite.addEventListener('change', (event) => {
			if (event.target.checked) { // Пересоздавать учащегося
				event.target.parentElement.querySelector('label').innerHTML =
					"{{ $index }}. Выбран режим &laquo;Пересоздавать учащегося при совпадении электронной почты&raquo;";
			} else { // НЕ пересоздавать учащегося
				event.target.parentElement.querySelector('label').innerHTML =
					"{{ $index }}. Выбран режим &laquo;НЕ пересоздавать учащегося при совпадении электронной почты&raquo;";
			}
		}, false);

		let upload = document.getElementById('upload');
		upload.addEventListener('change', (event) => {
			document.getElementById('btn-import').disabled = event.target.value === '';
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			rewrite.dispatchEvent(new Event('change'));
			upload.dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
