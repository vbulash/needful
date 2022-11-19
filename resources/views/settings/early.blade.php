@extends('layouts.detail')

@section('header')
	Настройки платформы &laquo;{{ env('APP_NAME') }}&raquo;
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Письма перед началом практики', 'active' => true, 'context' => null]];
	@endphp
@endsection

@section('interior.header')
	Перед началом практики отправляются специальные письма. Укажите здесь количество дней до начала практики = момент,
	когда будет отправлено соответствующее письмо
@endsection

@php
	if (isset($early)) {
	    $temp = json_decode($early);
	    $cancel = $temp->cancel;
	    $last = $temp->last;
	} else {
	    $cancel = 7;
	    $last = 1;
	}
@endphp

@section('form.params')
	id="early-form" name="early-form"
	action="{{ route('settings.early.store') }}"
@endsection

@section('form.fields')
	@php
		$fields = [
			['name' => 'cancel', 'title' => 'Письмо работодателю о последней возможности отмены практики', 'required' => true, 'type' => 'number', 'min' => 1, 'value' => $cancel],
			['name' => 'last', 'title' => 'Письмо-предупреждение работодателю и практикантам о начале практики', 'required' => true, 'type' => 'number', 'min' => 1, 'value' => $last],
		];
	@endphp
@endsection

@section('form.close')
	{{ route('dashboard') }}
@endsection

@push('js_after')
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			//
		}, false);
	</script>
@endpush
