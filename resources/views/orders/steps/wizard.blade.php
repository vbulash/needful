@extends('layouts.wizard')

@section('steps')
	@php
		$index = 0;
		$steps = [];
		foreach (\App\Http\Controllers\orders\StepController::$steps as $stepClass) {
            $step = new $stepClass();
			$active = $index++ == \App\Http\Controllers\orders\StepController::getCurrentStep();
            $steps[] = [
                'title' => $step->getTitle(),
                'active' => $active,
                'context' => $step->getContext(),
			];
		}
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
