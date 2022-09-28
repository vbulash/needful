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
	Просмотр анкеты пользователя &laquo;{{ $user->name }}&raquo;
@endsection

@section('form.params')
	id="{{ form($user, $mode, 'id') }}" name="{{ form($user, $mode, 'name') }}"
	action="{{ form($user, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'name', 'title' => 'Фамилия, имя и отчество', 'required' => true, 'type' => 'text', 'value' => $user->name],
			['name' => 'email', 'title' => 'Электронная почта', 'required' => false, 'type' => 'email', 'value' => $user->email],
			['name' => 'role', 'title' => 'Роль пользователя', 'required' => false, 'type' => 'text', 'value' => $user->getRoleNames()->join(" / ")],
		];
	@endphp
@endsection

@section('form.close')
	{{ form($user, $mode, 'close') }}
@endsection
