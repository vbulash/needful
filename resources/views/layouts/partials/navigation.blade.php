<div class="content-side content-side-full">
	@php
		$employers = false;
        $students = false;
        $schools = false;
        $admin = false;
		if (auth()->user()->hasRole('Администратор')) {
			$employers = true;
			$students = true;
            $schools = true;
            $admin = true;
		} else {
            $employers = auth()->user()->hasRole('Работодатель');
			$students = auth()->user()->hasRole('Практикант');
			$schools = auth()->user()->hasRole('Учебное заведение');
		}

        $menu = [
            ['title' => 'Главная', 'icon' => 'fa fa-home', 'route' => 'dashboard', 'pattern' => 'dashboard'],

            ['title' => 'Стажировки', 'heading' => true],
            ['title' => 'История стажировок', 'icon' => 'fas fa-history', 'route' => 'history.index', 'pattern' => ['history.*']],
        ];

        if ($employers || $students || $schools)
            $menu[] = ['title' => 'Субъекты', 'heading' => true];

        if ($employers)
            $menu[] = ['title' => 'Работодатели', 'icon' => 'fas fa-business-time', 'route' => 'employers.index', 'pattern' => ['employers.*', 'internships.*', 'especialties.*', 'timetables.*']];
        if ($schools)
            $menu[] = ['title' => 'Учебные заведения', 'icon' => 'fas fa-university', 'route' => 'schools.index', 'pattern' => ['schools.*', 'fspecialties.*']];
        if ($schools || $employers)
            $menu[] = ['title' => 'Руководители практики', 'icon' => 'fas fa-users-cog', 'route' => 'teachers.index', 'pattern' => ['teachers.*', 'tstudents.*']];
		if ($students)
            $menu[] = ['title' => 'Учащиеся', 'icon' => 'fas fa-gear', 'route' => 'students.index', 'pattern' => ['students.*']];

        $menu[] = ['title' => 'Справочники', 'heading' => true];
        $menu[] = ['title' => 'Специальности', 'icon' => 'fas fa-book', 'route' => 'specialties.index', 'pattern' => ['specialties.*']];

        if ($admin) {
            $menu[] = ['title' => 'Настройки', 'heading' => true];
            $menu[] = ['title' => 'Пользователи', 'icon' => 'fa fa-user-alt', 'route' => 'users.index', 'pattern' => 'users.*'];
            $menu[] = ['title' => 'Laravel Telescope', 'icon' => 'fa fa-gears', 'route' => 'telescope', 'pattern' => 'telescope'];
		}
	@endphp
	<ul class="nav-main">
		@foreach($menu as $item)
			@if(isset($item['heading']))
				<li class="nav-main-heading">{{ $item['title'] }}</li>
			@else
				<li class="nav-main-item">
					<a class="nav-main-link{{ request()->routeIs($item['pattern']) ? ' active' : '' }}"
					   href="{{ route($item['route'], ['sid' => session()->getId()]) }}">
						<i class="nav-main-link-icon {{ $item['icon'] }}"></i>
						<span class="nav-main-link-name">{{ $item['title'] }}</span>
					</a>
				</li>
			@endif
		@endforeach
	</ul>
</div>
