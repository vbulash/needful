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
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	пользователя &laquo;{{ $user->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($user, $mode, 'id') }}" name="{{ form($user, $mode, 'name') }}"
	action="{{ form($user, $mode, 'action') }}"
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
			['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text', 'value' => $user->name],
			['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email', 'value' => $user->email],
			['name' => 'password', 'title' => 'Новый пароль', 'required' => false, 'type' => 'password', 'generate' => true],
			['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => false, 'type' => 'password'],
			['name' => 'role', 'title' => 'Роль пользователя', 'required' => true, 'type' => 'select', 'options' => $roles, 'value' => $user->getRoleNames()->join(" / ")],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($user, $mode, 'close') }}
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
		});
	</script>
@endpush
