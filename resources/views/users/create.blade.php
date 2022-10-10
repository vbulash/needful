@extends('layouts.detail')

@section('header')<div class="mt-4"></div>@endsection

@section('steps')
	@php
		$steps = [
			['title' => 'Пользователи', 'active' => true, 'context' => 'user', 'link' => route('users.index', ['sid' => session()->getId()])],
		];
	@endphp
@endsection

@section('interior.header')
	Новый пользователь
	@if (isset($for) && $for)
		<br/>
		<small>Вы можете не сообщать пользователю его пароль. В этом случае он может воспользоваться функцией восстановления пароля при входе в &laquo;{{ env('APP_NAME') }}&raquo; -
			соответствующее предложение будет направлено ему в письме по итогам создания данного пользователя</small>
	@endif
@endsection

@section('form.params')
	id="{{ form(\App\Models\User::class, $mode, 'id') }}" name="{{ form(\App\Models\User::class, $mode, 'name') }}"
	action="{{ form(\App\Models\User::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)) {
            $roles = [
                \App\Http\Controllers\Auth\RoleName::ADMIN->value => \App\Http\Controllers\Auth\RoleName::ADMIN->value,
                \App\Http\Controllers\Auth\RoleName::TRAINEE->value => \App\Http\Controllers\Auth\RoleName::TRAINEE->value,
                \App\Http\Controllers\Auth\RoleName::EMPLOYER->value => \App\Http\Controllers\Auth\RoleName::EMPLOYER->value,
			];
		} elseif (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value)) {
            $roles = [
                \App\Http\Controllers\Auth\RoleName::TRAINEE->value => \App\Http\Controllers\Auth\RoleName::TRAINEE->value,
			];
        } elseif (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::EMPLOYER->value)) {
            $roles = [
                \App\Http\Controllers\Auth\RoleName::EMPLOYER->value => \App\Http\Controllers\Auth\RoleName::EMPLOYER->value,
			];
		}

        $fields = [
            ['name' => 'for', 'type' => 'hidden', 'value' => isset($for) && $for ? $for->getKey() : false]
		];
        if (isset($for) && $for) {
            $fields[] = ['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text', 'value' => $for->getTitle()];
            $fields[] = ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $for->email];
        } else {
            $fields[] = ['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text'];
            $fields[] = ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'];
        }
		$fields[] = ['name' => 'password', 'title' => 'Новый пароль', 'required' => true, 'type' => 'password', 'generate' => true];
        $fields[] = ['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => true, 'type' => 'password'];
        if (isset($for) && $for)
			$fields[] = ['name' => 'role', 'title' => 'Роль пользователя', 'required' => true, 'type' => 'select', 'options' => $roles, 'value' => \App\Http\Controllers\Auth\RoleName::TRAINEE->value];
        else
            $fields[] = ['name' => 'role', 'title' => 'Роль пользователя', 'required' => true, 'type' => 'select', 'options' => $roles];
	@endphp
@endsection

@section('form.close')
	@if (isset($for) && $for)
		javascript:void(0)
	@else
		{{ form(App\Models\User::class, $mode, 'close') }}
	@endif
@endsection

@push('js_after')
	<script>
		$(function () {
			$("#get-password").on("click", (event) => {
				event.preventDefault();
				$.post({
					url: "{{ route('api.get.password', ['length' => 10]) }}",
					datatype: "json",
					success: (helper) => {
						$("#password").val(helper.password);
					}
				});
			});

			@if (isset($for) && $for)
				$('#button-close').hide();
				$.post({
					url: "{{ route('api.get.password', ['length' => 20]) }}",
					datatype: "json",
					success: (helper) => {
						$("#password").val(helper.password);
						$("#password_confirmation").val(helper.password);
					}
				});
			@endif
		});
	</script>
@endpush
