@hasrole('Администратор')
@php
	$employers = true;
	$students = true;
@endphp
@endhasrole
@hasrole('Работодатель')
@php
	$employers = true;
	$students = false;
@endphp
@endhasrole
@hasrole('Практикант')
@php
	$employers = false;
	$students = true;
@endphp
@endhasrole
<div class="content-side content-side-full">
	<ul class="nav-main">
		<li class="nav-main-item">
			<a class="nav-main-link{{ request()->routeIs('dashboard') ? ' active' : '' }}"
			   href="{{ route('dashboard') }}">
				<i class="nav-main-link-icon fa fa-home"></i>
				<span class="nav-main-link-name">Главная</span>
			</a>
		</li>
		<li class="nav-main-heading">Лица</li>
		@if($employers)
			<li class="nav-main-item">
				<a class="nav-main-link{{ request()->routeIs('employers.*') ? ' active' : '' }}"
				   href="{{ route('employers.index') }}">
					<i class="nav-main-link-icon fa fa-business-time"></i>
					<span class="nav-main-link-name">Работодатели</span>
				</a>
			</li>
		@endif
		@if($students)
			<li class="nav-main-item">
				<a class="nav-main-link{{ request()->routeIs('students.*') ? ' active' : '' }}"
				   href="{{ route('students.index') }}">
					<i class="nav-main-link-icon fa fa-gear"></i>
					<span class="nav-main-link-name">Практиканты</span>
				</a>
			</li>
		@endif

		@hasrole('Администратор')
		<li class="nav-main-heading">Настройки</li>
		{{--		<ul class="nav-main-submenu">--}}
		@can('users.list')
			<li class="nav-main-item">
				<a class="nav-main-link" href="{{ route('users.index', ['sid' => session()->getId()]) }}">
					<i class="nav-main-link-icon fa fa-user-alt"></i>
					<span class="nav-main-link-name">Пользователи</span>
				</a>
			</li>
		@endcan
		<li class="nav-main-item">
			<a class="nav-main-link" href="{{ route('telescope') }}">
				<i class="nav-main-link-icon fa fa-gears"></i>
				<span class="nav-main-link-name">Laravel Telescope</span>
			</a>
		</li>
		@endhasrole
	</ul>
</div>
