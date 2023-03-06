@extends('layouts.detail')

@section('header')
@endsection

@section('steps')
	@php
		$steps = [['title' => 'Учащийся', 'active' => false, 'context' => 'student', 'link' => route('students.index', ['sid' => session()->getId()])], ['title' => 'История обучения', 'active' => true, 'context' => 'learn', 'link' => route('learns.index', ['sid' => session()->getId()])]];
	@endphp
@endsection

@section('interior.header')
	@if ($mode == config('global.show'))
		Просмотр
	@else
		Редактирование
	@endif
	записи истории обучения &laquo;{{ $learn->getTitle() }}&raquo;<br />
	<small>Оставьте дату завершения незаполненной для текущего (последнего) образовательного учреждения</small>
@endsection

@section('form.params')
	id="{{ form($learn, $mode, 'id') }}" name="{{ form($learn, $mode, 'name') }}"
	action="{{ form($learn, $mode, 'action') }}"
@endsection

@section('form.fields')
	@php
		$fields = [];
		if (
		    auth()
		        ->user()
		        ->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)
		) {
		    $fields[] = [
		        'name' => 'status',
		        'title' => 'Статус активности объекта',
		        'required' => false,
		        'type' => 'select',
		        'options' => [
		            \App\Models\ActiveStatus::NEW->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::NEW->value),
		            \App\Models\ActiveStatus::ACTIVE->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::ACTIVE->value),
		            \App\Models\ActiveStatus::FROZEN->value => \App\Models\ActiveStatus::getName(\App\Models\ActiveStatus::FROZEN->value),
		        ],
		        'value' => $learn->status,
		    ];
		} else {
		    $fields[] = ['name' => 'status', 'type' => 'hidden', 'value' => $learn->status];
		}
		$fields[] = ['name' => 'start', 'title' => 'Дата поступления', 'required' => true, 'type' => 'date', 'value' => $learn->start->format('d.m.Y')];
		$item = ['name' => 'finish', 'title' => 'Дата завершения', 'required' => false, 'type' => 'date'];
		if ($learn->finish) {
		    $item['value'] = $learn->finish->format('d.m.Y');
		}
		$fields[] = $item;
		if ($learn->status == \App\Models\ActiveStatus::ACTIVE->value) {
		    $item = ['name' => 'school_id', 'title' => 'Образовательное учреждение', 'required' => false, 'type' => 'select', 'options' => $schools, 'value' => $learn->school->getKey()];
		    //if (!auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)) $item['disabled'] = true;
		    $fields[] = $item;
		    $item = ['name' => 'specialty_id', 'title' => 'Специальность', 'required' => false, 'type' => 'select', 'options' => $specialties, 'value' => $learn->specialty->getKey()];
		    //if (!auth()->user()->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)) $item['disabled'] = true;
		    $fields[] = $item;
		} elseif ($learn->status == \App\Models\ActiveStatus::NEW->value) {
		    $fields[] = ['title' => 'Образовательное учреждение', 'type' => 'heading'];
		    $item = ['name' => 'school_id', 'title' => 'Образовательное учреждение', 'required' => false, 'type' => 'select', 'options' => $schools];
		    if ($learn->school) {
		        $item['value'] = $learn->school->getKey();
		    }
		    $fields[] = $item;
		    $fields[] = ['name' => 'new_school', 'title' => 'Новое образовательное учреждение (нет в списке)', 'required' => false, 'type' => 'text', 'value' => $learn->new_school];
		
		    $fields[] = ['title' => 'Специальность', 'type' => 'heading'];
		    $item = ['name' => 'specialty_id', 'title' => 'Специальность', 'required' => false, 'type' => 'select', 'options' => $specialties];
		    if ($learn->specialty) {
		        $item['value'] = $learn->specialty->getKey();
		    }
		    $fields[] = $item;
		    $fields[] = ['name' => 'new_specialty', 'title' => 'Новая специальность (нет в списке)', 'required' => false, 'type' => 'text', 'value' => $learn->new_specialty];
		}
	@endphp
@endsection

@section('form.close')
	{{ form($learn, $mode, 'close') }}
@endsection

@push('js_after')
	<script>
		let form = document.getElementById("{{ form($learn, $mode, 'id') }}");
		form.addEventListener('submit', () => {
			//
		}, false);
	</script>
@endpush
