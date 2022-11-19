@extends('students.edit')

@section('header')
	@if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value))
		Мои практики
	@else
		Работа с практиками
	@endif
@endsection

@section('steps')
	@php
//		$steps = [
//			['title' => 'Практика', 'active' => false, 'context' => 'history', 'link' => route('history.index')],
//			['title' => 'Практиканты', 'active' => true, 'context' => 'trainee', 'link' => route('trainees.index')],
//		];
	@endphp
@endsection

@section('interior.header')
	@if($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	анкеты практиканта &laquo;{{ $student->getTitle() }}&raquo;
@endsection

@section('form.close')
	{{ route('trainees.index') }}
@endsection
