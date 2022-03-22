@extends('layouts.backend')

@section('content')
	<!-- Content Header (Page header) -->
	<div class="bg-body-light">
		<div class="content content-full">
			<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
				<h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Новый практикант</h1>
				<nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">Лица</li>
						<li class="breadcrumb-item active" aria-current="page">Практиканты</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<div class="content p-3">
		<div class="block block-rounded">
			<div class="block-header block-header-default">
				<p><span class="required">*</span> - поля, обязательные для заполнения</p>
				@hasrole('Администратор')
				<div class="row mb-4">
					<label class="col-sm-5 col-form-label" for="link">Свяжите данную анкету практиканта с записью пользователя</label>
					<div class="col-sm-7">
						<select name="link" id="link">
							<option selected disabled>Выберите пол</option>
							<option value="Мужской">Мужской</option>
							<option value="Женский">Женский</option>
						</select>
					</div>
				</div>
				@endhasrole
			</div>
			<div class="block-content pb-3">
				<form role="form" class="mb-5" method="post"
					  action="{{ route('students.store', ['sid' => session()->getId()]) }}"
					  autocomplete="off" enctype="multipart/form-data">
					@csrf
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="lastname">Фамилия <span
								class="required">*</span></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="lastname" name="lastname">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="firstname">Имя <span
								class="required">*</span></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="firstname" name="firstname">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="surname">Отчество</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="surname" name="surname">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="sex">Пол <span class="required">*</span></label>
						<div class="col-sm-5">
							<select name="sex" id="sex">
								<option selected disabled>Выберите пол</option>
								<option value="Мужской">Мужской</option>
								<option value="Женский">Женский</option>
							</select>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="phone">Телефон <span
								class="required">*</span></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="phone" name="phone">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="email">Электронная почта <span
								class="required">*</span></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="email" name="email">
						</div>
					</div>
					<div class="row mb-4 date">
						<label class="col-sm-3 col-form-label" for="birthdate">Дата рождения <span
								class="required">*</span></label>
						<div class="col-sm-5">
							{{--							<input type="text" class="form-control" id="birthdate" name="birthdate">--}}
							<input type="text" class="form-control flatpickr-input active" id="birthdate"
								   name="birthdate" data-date-format="d.m.Y">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="parents">ФИО родителей, опекунов (до 14 лет), после
							14 лет можно не указывать</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="parents" id="parents" cols="30" rows="5"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="parentscontact">Контактные телефоны родителей или
							опекунов</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="parentscontact" id="parentscontact" cols="30"
									  rows="5"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="passport">Данные паспорта (серия, номер, кем и когда
							выдан)</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="passport" id="passport" cols="30" rows="5"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="address">Адрес проживания</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="address" id="address" cols="30" rows="5"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="institutions">Учебное заведение (на момент
							заполнения)</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="institutions" id="institutions" cols="30"
									  rows="5"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="grade">Класс / группа (на момент заполнения)</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="grade" name="grade">
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="hobby">Увлечения (хобби)</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="hobby" id="hobby" cols="30" rows="5"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="hobbyyears">Как давно занимается хобби
							(лет)?</label>
						<div class="col-sm-5">
							<input type="number" class="form-control" id="hobbyyears" name="hobbyyears">
						</div>
					</div>
					{{--					<div class="row mb-4 date">--}}
					{{--						<label class="col-sm-3 col-form-label" for="hobbyachievements">Есть ли достижения, полученные благодаря хобби?</label>--}}
					{{--						<div class="col-sm-5">--}}
					{{--							<textarea class="form-control" name="hobbyachievements" id="hobbyachievements" cols="30" rows="10"></textarea>--}}
					{{--						</div>--}}
					{{--					</div>--}}
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="contestachievements">Участие в конкурсах,
							олимпиадах. Достижения</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="contestachievements" id="contestachievements" cols="30"
									  rows="5"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label class="col-sm-3 col-form-label" for="dream">Чем хочется заниматься в жизни?</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="dream" id="dream" cols="30" rows="5"></textarea>
						</div>
					</div>
					{{--					TODO: реализовать browse_multiple (elFinder?) для хранения документов --}}
					{{-- $this->crud->field('documents')->label('Документы')->type('browse_multiple'); --}}

					<div class="row mb-4">
						<div class="col-sm-3 col-form-label">&nbsp;</div>
						<div class="col-sm-5">
							<button type="submit" class="btn btn-primary">Сохранить</button>
							<a class="btn btn-secondary pl-3"
							   href="{{ route('students.index', ['sid' => session()->getId()]) }}"
							   role="button">Закрыть</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection
