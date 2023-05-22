@extends('layouts.skeleton')

@section('body')
	<div id="page-container">

		<!-- Main Container -->
		<main id="main-container">
			<!-- Page Content -->
			<div class="bg-image" style="background-image: url('{{ asset('media/photos/moscow_city_12.jpeg') }}');">
				<div class="row g-0 justify-content-center bg-primary-dark-op">
					<div class="hero-static col-sm-8 col-md-6 col-xl-4 d-flex align-items-center p-2 px-sm-0">
						<!-- Sign In Block -->
						<div class="block block-transparent block-rounded w-100 mb-0 overflow-hidden">
							<div class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-body-extra-light">
								<!-- Header -->
								<div class="mb-2 text-center">
									<a class="link-fx fw-bold fs-1" href="javascript:void(0)">
										<span class="text-dark">{!! env('APP_NAME') !!}</span>
									</a>
									<p class="text-uppercase fw-bold fs-sm text-muted">Регистрация новой учетной
										записи</p>
								</div>
								<!-- END Header -->

								<form method="POST" action="{{ route('register') }}" autocomplete="off">
									@csrf
									<div class="form-floating mb-4">
										<input type="text" class="form-control" id="signup-username" name="name"
											placeholder="Фамилия, имя и отчество" autocomplete="off">
										<label for="name">Фамилия, имя и отчество</label>
									</div>
									<div class="form-floating mb-4">
										<input type="email" class="form-control" id="signup-email" name="email" placeholder="Электронная почта"
											autocomplete="off">
										<label for="email">Электронная почта</label>
									</div>
									<div class="form-floating mb-4">
										<input type="password" class="form-control" id="signup-password" name="password" placeholder="Пароль">
										<label for="password">Пароль</label>
									</div>
									<div class="form-floating mb-4">
										<input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
											placeholder="Подтверждение пароля">
										<label for="password_confirmation">Подтверждение пароля</label>
									</div>

									<div class="form-floating mb-4">
										<select name="role" id="role" class="form-control">
											@foreach ($roles as $role)
												<option value="{{ $role }}" @if ($loop->first) selected @endif>{!! $role !!}
												</option>
											@endforeach
										</select>
										<label for="password_confirmation">Роль нового пользователя</label>
									</div>

									<div class="d-sm-flex justify-content-sm-between align-items-sm-center mb-4 bg-body rounded py-2 px-3">
										<div class="form-check">
											<input type="checkbox" class="signup form-check-input" id="terms" name="terms">
											<label class="form-check-label" for="terms">Я соглашаюсь с Политикой
												конфиденциальности</label>
										</div>
										<div class="fw-semibold fs-sm py-1">
											<a class="fw-semibold fs-sm" href="#" data-bs-toggle="modal" data-bs-target="#modal-terms">Политика
												конфиденциальности</a>
										</div>
									</div>
									<div class="text-center mb-4">
										<button type="submit" class="btn btn-hero btn-primary" id="submit_btn">
											<i class="fa fa-fw fa-plus opacity-50 me-1"></i> Зарегистрировать
										</button>
									</div>
								</form>
							</div>
						</div>
						<!-- END Sign In Block -->
					</div>
				</div>

				<!-- Terms Modal -->
				<div class="modal fade" id="modal-terms" tabindex="-1" role="dialog" aria-labelledby="modal-terms"
					aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
							<div class="block block-themed block-transparent mb-0">
								<div class="block-header bg-success">
									<h3 class="block-title">Политика конфиденциальности</h3>
									<div class="block-options">
										<button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
											<i class="fa fa-fw fa-times"></i>
										</button>
									</div>
								</div>
								<div class="block-content">
									<p>Potenti elit lectus augue eget iaculis vitae etiam, ullamcorper etiam bibendum ad
										feugiat magna accumsan dolor, nibh molestie cras hac ac ad massa, fusce ante
										convallis ante urna molestie vulputate bibendum tempus ante justo arcu erat
										accumsan adipiscing risus, libero condimentum venenatis sit nisl nisi ultricies
										sed, fames aliquet consectetur consequat nostra molestie neque nullam
										scelerisque neque commodo turpis quisque etiam egestas vulputate massa,
										curabitur tellus massa venenatis congue dolor enim integer luctus, nisi suscipit
										gravida fames quis vulputate nisi viverra luctus id leo dictum lorem, inceptos
										nibh orci.</p>
									<p>Potenti elit lectus augue eget iaculis vitae etiam, ullamcorper etiam bibendum ad
										feugiat magna accumsan dolor, nibh molestie cras hac ac ad massa, fusce ante
										convallis ante urna molestie vulputate bibendum tempus ante justo arcu erat
										accumsan adipiscing risus, libero condimentum venenatis sit nisl nisi ultricies
										sed, fames aliquet consectetur consequat nostra molestie neque nullam
										scelerisque neque commodo turpis quisque etiam egestas vulputate massa,
										curabitur tellus massa venenatis congue dolor enim integer luctus, nisi suscipit
										gravida fames quis vulputate nisi viverra luctus id leo dictum lorem, inceptos
										nibh orci.</p>
								</div>
								<div class="block-content block-content-full text-end bg-body">
									<button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Готово
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- END Terms Modal -->
			</div>
			<!-- END Page Content -->
		</main>
		<!-- END Main Container -->
	</div>
	<!-- END Page Container -->
@endsection

@push('js_after')
	<script>
		document.getElementById('terms').addEventListener('change', event => {
			document.getElementById('submit_btn').disabled = !event.target.checked;
		}, false);

		document.addEventListener("DOMContentLoaded", () => {
			document.getElementById('terms').dispatchEvent(new Event('change'));
		}, false);
	</script>
@endpush
