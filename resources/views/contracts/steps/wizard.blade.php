@extends('layouts.wizard')

@section('steps')
	@php
		$index = 0;
		$steps = [];
		foreach (\App\Http\Controllers\contracts\StepController::$steps as $stepClass) {
		    $step = new $stepClass();
		    $active = $index++ == \App\Http\Controllers\contracts\StepController::getCurrentStep();
		    $steps[] = [
		        'title' => $step->getTitle(),
		        'active' => $active,
		        'context' => $step->getContext(),
		    ];
		}
		// \Illuminate\Support\Facades\Log::info($steps);
	@endphp
@endsection

@section('form.params')
	id="core-create" name="core-create"
	@if ($buttons & \App\Http\Controllers\WizardButtons::NEXT->value)
		action="{{ route('contracts.steps.next') }}"
	@elseif ($buttons & \App\Http\Controllers\WizardButtons::FINISH->value)
		action="{{ route('contracts.steps.finish') }}"
	@endif
@endsection

@section('form.close')
	{{ route('contracts.steps.close') }}
@endsection

@section('form.back')
	{{ route('contracts.steps.back') }}
@endsection
