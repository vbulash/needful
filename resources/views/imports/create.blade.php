@extends('layouts.chain')

@section('steps')
    @php
        $steps = [['title' => 'Импорт учащихся', 'active' => true, 'context' => 'null']];
    @endphp
@endsection

@section('interior')
    <div class="block-header block-header-default">
        <div>
            <p>Здесь вы можете скачать шаблон таблицы для заполнения данными учащихся:</p>
            <button class="btn btn-secondary" id="download-template">Скачать шаблон таблицы</button>
        </div>
    </div>

    <div class="block-content p-4">
        <div>
            <p>Заполненный шаблон таблицы необходимо импортировать по кнопке</p>
            <button class="btn btn-primary mb-4" id="import-students">Импортировать учащихся</button>
            <p>
				<small>
					Обращаем ваше внимание: при импорте учащихся будет происходить попытка создания пользователя с адресом
                    электронной почты, указанной в строке таблицы. Если пользователь с такой электронной почтой уже
                    существует, то будет происходить привязка импортированного учащегося к данному пользователю. Для новых
                    пользователей будет создаваться случайно сгенерированный пароль, который пользователь не узнает, но
                    сможет восстановить через функцию &laquo;Забыли пароль&raquo; на диалоге входа в платформу
                    &laquo;{{ env('APP_NAME') }}&raquo;
                </small>
			</p>
        </div>
    </div>
@endsection
