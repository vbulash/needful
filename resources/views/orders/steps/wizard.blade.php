@extends('layouts.wizard')

@section('steps')
	@php
		$index = 0;
		$steps = [];
		foreach (\App\Http\Controllers\orders\StepController::$steps as $stepClass) {
            $step = new $stepClass();
            $steps[] = [
                'title' => $step->getTitle(),
                'active' => ($index++ == \App\Http\Controllers\orders\StepController::getCurrentStep()),
                'context' => $step->getContext(),
			];
		}
		$steps[] = [
			'title' => 'Уведомление работодателей',
			'active' => false,
			'context' => null
		];
		$steps[] = [
			'title' => 'Подтверждение выбора',
			'active' => false,
			'context' => null
		];
	@endphp
@endsection

@section('form.params')
	id="core-create" name="core-create"
	@if ($buttons & \App\Http\Controllers\WizardButtons::NEXT->value)
		action="{{ route('orders.steps.next') }}"
	@elseif ($buttons & \App\Http\Controllers\WizardButtons::FINISH->value)
		action="{{ route('orders.steps.finish') }}"
	@endif
@endsection

@section('form.close')
	{{ route('orders.steps.close') }}
@endsection

@section('form.back')
	{{ route('orders.steps.back') }}
@endsection

