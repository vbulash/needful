@extends('layouts.detail')

@section('header')
@endsection

@section('steps')
    @php
        $steps = [['title' => 'Учащиеся', 'active' => true, 'context' => 'student', 'link' => route('students.index', ['sid' => session()->getId()])], ['title' => 'История обучения', 'active' => false, 'context' => 'learn']];
    @endphp
@endsection

@section('interior.header')
    Новый учащийся
@endsection

@section('form.params')
    id="{{ form(\App\Models\Student::class, $mode, 'id') }}" name="{{ form(\App\Models\Student::class, $mode, 'name') }}"
    action="{{ form(\App\Models\Student::class, $mode, 'action') }}"
@endsection

@section('form.fields')
    @include('students.assign')
    @php
        $fields = [];
        $fields[] = ['name' => 'status', 'type' => 'hidden', 'value' => \App\Models\ActiveStatus::NEW->value];
        $fields[] = ['name' => 'lastname', 'title' => 'Фамилия', 'required' => true, 'type' => 'text'];
        $fields[] = ['name' => 'firstname', 'title' => 'Имя', 'required' => true, 'type' => 'text'];
        $fields[] = ['name' => 'surname', 'title' => 'Отчество', 'required' => false, 'type' => 'text'];
        $fields[] = [
            'name' => 'sex',
            'title' => 'Пол',
            'required' => true,
            'type' => 'select',
            'options' => [
                'Мужской' => 'Мужской',
                'Женский' => 'Женский',
            ],
        ];
        $fields[] = ['name' => 'birthdate', 'title' => 'Дата рождения', 'required' => true, 'type' => 'date'];
        $fields[] = ['name' => 'phone', 'title' => 'Телефон', 'required' => true, 'type' => 'text'];
        if (isset($for)) {
            $fields[] = ['name' => 'email_show', 'title' => 'Электронная почта', 'required' => false, 'type' => 'email', 'value' => $for, 'disabled' => true];
			$fields[] = ['name' => 'email', 'type' => 'hidden', 'value' => $for];
        } else {
            $fields[] = ['name' => 'email', 'title' => 'Электронная почта', 'required' => true, 'type' => 'email'];
        }
        $fields[] = ['name' => 'parents', 'title' => 'ФИО родителей (до 14 лет), после 14 лет можно не указывать', 'required' => false, 'type' => 'textarea'];
        $fields[] = ['name' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов', 'required' => false, 'type' => 'textarea'];
        $fields[] = ['name' => 'passport', 'title' => 'Данные документа, удостоверяющего личность (серия, номер, кем и когда выдан)', 'required' => false, 'type' => 'textarea'];
        $fields[] = ['name' => 'address', 'title' => 'Адрес проживания', 'required' => false, 'type' => 'textarea'];
        $fields[] = ['name' => 'grade', 'title' => 'Класс / группа (на момент заполнения)', 'required' => false, 'type' => 'text'];
        $fields[] = ['name' => 'hobby', 'title' => 'Увлечения (хобби)', 'required' => false, 'type' => 'textarea'];
        $fields[] = ['name' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?', 'required' => false, 'type' => 'number'];
        $fields[] = ['name' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения', 'required' => false, 'type' => 'textarea'];
        $fields[] = ['name' => 'dream', 'title' => 'Чем хочется заниматься в жизни?', 'required' => false, 'type' => 'textarea'];
    @endphp
@endsection

@section('form.close')
    {{ form(\App\Models\Student::class, $mode, 'close') }}
@endsection

@push('js_after')
    <script>
        let form = document.getElementById("{{ form(\App\Models\Student::class, $mode, 'id') }}");
        form.addEventListener('submit', () => {
            //$('#specialties').val(JSON.stringify($('#hspecialties').val()));
        }, false);
    </script>
@endpush
