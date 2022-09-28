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
@endsection

@section('form.params')
	id="{{ form(\App\Models\User::class, $mode, 'id') }}" name="{{ form(App\Models\User::class, $mode, 'name') }}"
	action="{{ form(App\Models\User::class, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		if (auth()->user()->hasRole('Администратор')) {
            $roles = [
                'Администратор' => 'Администратор',
                'Практикант' => 'Практикант',
                'Работодатель' => 'Работодатель'
			];
		} elseif (auth()->user()->hasRole('Практикант')) {
            $roles = [
                'Практикант' => 'Практикант',
			];
        } elseif (auth()->user()->hasRole('Работодатель')) {
            $roles = [
                'Работодатель' => 'Работодатель'
			];
		}
		$fields = [
			['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text'],
			['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'],
			['name' => 'password', 'title' => 'Новый пароль', 'required' => true, 'type' => 'password', 'generate' => true],
			['name' => 'password_confirmation', 'title' => 'Повторный ввод пароля', 'required' => true, 'type' => 'password'],
			['name' => 'role', 'title' => 'Роль пользователя', 'required' => true, 'type' => 'select', 'options' => $roles],
		];
	@endphp
@endsection

@section('form.close')
	{{ form(App\Models\User::class, $mode, 'close') }}
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
