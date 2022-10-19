@extends('students.edit')

@section('header')
	@if (auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value))
		Мои стажировки
	@else
		Работа со стажировками
	@endif
@endsection

@section('steps')
	@php
//		$steps = [
//			['title' => 'Стажировка', 'active' => false, 'context' => 'history', 'link' => route('history.index')],
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
