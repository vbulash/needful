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

		<li class="nav-main-heading">Various</li>
		<li class="nav-main-item{{ request()->is('pages/*') ? ' open' : '' }}">
			<a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
			   aria-expanded="true" href="#">
				<i class="nav-main-link-icon fa fa-users"></i>
				<span class="nav-main-link-name">Лица</span>
			</a>
			<ul class="nav-main-submenu">
				<li class="nav-main-item">
					<a class="nav-main-link{{ request()->is('pages/datatables') ? ' active' : '' }}"
					   href="/pages/datatables">
						<span class="nav-main-link-name">DataTables</span>
					</a>
				</li>
				<li class="nav-main-item">
					<a class="nav-main-link{{ request()->is('pages/slick') ? ' active' : '' }}" href="/pages/slick">
						<span class="nav-main-link-name">Slick Slider</span>
					</a>
				</li>
				<li class="nav-main-item">
					<a class="nav-main-link{{ request()->is('pages/blank') ? ' active' : '' }}" href="/pages/blank">
						<span class="nav-main-link-name">Blank</span>
					</a>
				</li>
			</ul>
		</li>
		<li class="nav-main-heading">Лица</li>
		@hasrole('Работодатель')
		<li class="nav-main-item">
			<a class="nav-main-link{{ request()->routeIs('employers.*') ? ' active' : '' }}" href="{{ route('employers.index') }}">
				<i class="nav-main-link-icon fa fa-globe"></i>
				<span class="nav-main-link-name">Работодатели</span>
			</a>
		</li>
		@endhasrole
		<li class="nav-main-item">
			<a class="nav-main-link" href="/">
				<i class="nav-main-link-icon fa fa-globe"></i>
				<span class="nav-main-link-name">Практиканты</span>
			</a>
		</li>

		@hasrole('Администратор')
		<li class="nav-main-heading">Настройки</li>
		{{--		<ul class="nav-main-submenu">--}}
		@can('users.list')
			<li class="nav-main-item">
				<a class="nav-main-link" href="{{ route('users.index', ['sid' => session()->getId()]) }}">
					<i class="nav-main-link-icon fa fa-globe"></i>
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
