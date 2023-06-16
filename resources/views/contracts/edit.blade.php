@extends('layouts.detail')

@section('service')
	Работа с договорами на практику
@endsection

@section('steps')
	@php
		$steps = [
		    [
		        'title' => 'Договор на практику',
		        'active' => true,
		        'context' => 'contract',
		        'link' => route('contracts.index'),
		    ],
		    [
		        'title' => 'Практиканты',
		        'active' => false,
		        'context' => 'contract.students',
		    ],
		];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	договора на практику &laquo;{{ $contract->getTitle() }}&raquo;
@endsection

@section('form.params')
	id="contract-edit" name="contract-edit"
	action="{{ route('contracts.update', ['contract' => $contract->getKey()]) }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		$fields[] = ['name' => 'school', 'title' => 'Название образовательного учреждения', 'required' => false, 'type' => 'text', 'value' => $contract->school->getTitle(), 'disabled' => true];
		$fields[] = ['name' => 'employer', 'title' => 'Название работодателя', 'required' => false, 'type' => 'text', 'value' => $contract->employer->getTitle(), 'disabled' => true];

		$fields[] = ['title' => 'Информация по договору', 'type' => 'heading'];
		$fields[] = ['name' => 'number', 'title' => 'Номер договора', 'required' => false, 'type' => 'text', 'value' => $contract->number];
		$fields[] = ['name' => 'sealed', 'title' => 'Дата подписания договора', 'required' => false, 'type' => 'date', 'value' => $contract->sealed->format('d.m.Y')];
		$fields[] = ['name' => 'start', 'title' => 'Дата начала практики', 'required' => false, 'type' => 'date', 'value' => $contract->start->format('d.m.Y')];
		$fields[] = ['name' => 'finish', 'title' => 'Дата завершения практики', 'required' => false, 'type' => 'date', 'value' => $contract->finish->format('d.m.Y')];
		if ($mode == config('global.show')) {
		    $fields[] = ['name' => 'scan', 'title' => 'Скан договора', 'required' => false, 'type' => 'link', 'value' => $contract->scan];
		} else {
			// if (isset($contract->scan)) {
			// 	$fields[] = ['title' => 'Скан договора загружен, ниже вы можете заменить его другим сканом', 'type' => 'heading'];
			// }
			$fields[] = ['name' => 'scan', 'title' => 'Скан договора', 'required' => false, 'type' => 'file'];
			if (isset($contract->scan)) {
				$fields[] = ['name' => 'clearscan', 'title' => 'НЕ удалять текущий загруженный скан договора', 'required' => false, 'type' => 'checkbox', 'value' => false];
			}
		}
	@endphp
@endsection

@section('form.close')
	{{ route('contracts.index') }}
@endsection

@push('js_after')
	<script>
		document.getElementById('clearscan').addEventListener('change', (event) => {
			document.getElementById('clearscan-label').innerHTML =
				(event.target.checked ?
					"Удалить текущий загруженный скан договора" :
					"НЕ удалять текущий загруженный скан договора");
			document.getElementById('scan').disabled = event.target.checked;
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('clearscan').dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
