<div class="content-side content-side-full">
	@php
		$employers = false;
		$students = false;
		$schools = false;
		$admin = false;
		if (
		    auth()
		        ->user()
		        ->hasRole(\App\Http\Controllers\Auth\RoleName::ADMIN->value)
		) {
		    $employers = true;
		    $students = true;
		    $schools = true;
		    $admin = true;
		} else {
		    $employers = auth()
		        ->user()
		        ->hasRole(\App\Http\Controllers\Auth\RoleName::EMPLOYER->value);
		    $students = auth()
		        ->user()
		        ->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value);
		    $schools = auth()
		        ->user()
		        ->hasRole(\App\Http\Controllers\Auth\RoleName::SCHOOL->value);
		}
		
		$menu = [['title' => 'Главная', 'icon' => 'fa fa-home', 'route' => 'dashboard', 'pattern' => 'dashboard']];
		
		$menu[] = ['title' => 'Практики', 'heading' => true];
		$name = auth()
		    ->user()
		    ->hasRole(\App\Http\Controllers\Auth\RoleName::TRAINEE->value)
		    ? 'Мои практики'
		    : 'Практики от работодателей';
		$menu[] = ['title' => $name, 'icon' => 'fas fa-history', 'route' => 'history.index', 'pattern' => ['history.*', 'trainees.*']];
		if ($admin) {
		    $menu[] = ['title' => 'Заявки от ОУ', 'icon' => 'fas fa-history', 'route' => 'orders.index', 'pattern' => ['orders.*']];
		}
		
		if ($employers || $students || $schools) {
		    $menu[] = ['title' => 'Субъекты', 'heading' => true];
		}
		
		if ($employers || $students) {
		    $menu[] = ['title' => 'Работодатели', 'icon' => 'fas fa-business-time', 'route' => 'employers.index.clear', 'pattern' => ['employers.*', 'internships.*', 'especialties.*', 'timetables.*']];
		}
		if ($schools) {
		    $menu[] = ['title' => 'Учебные заведения', 'icon' => 'fas fa-university', 'route' => 'schools.index', 'pattern' => ['schools.*', 'fspecialties.*']];
		}
		if ($schools || $employers) {
		    $menu[] = ['title' => 'Руководители практики', 'icon' => 'fas fa-users-cog', 'route' => 'teachers.index', 'pattern' => ['teachers.*']];
		}
		
		if (
		    auth()
		        ->user()
		        ->can('students.list')
		) {
		    $menu[] = ['title' => 'Учащиеся', 'icon' => 'fas fa-gear', 'route' => 'students.index', 'pattern' => ['students.*']];
		}
		
		if (!$students || $admin) {
		    $menu[] = ['title' => 'Справочники', 'heading' => true];
		    $menu[] = ['title' => 'Специальности', 'icon' => 'fas fa-book', 'route' => 'specialties.index', 'pattern' => ['specialties.*']];
		}
		
		if ($admin || $schools) {
		    $menu[] = ['title' => 'Настройки', 'heading' => true];
		    if ($admin) {
		        $menu[] = ['title' => 'Пользователи', 'icon' => 'fa fa-user-alt', 'route' => 'users.index', 'pattern' => 'users.*'];
		    }
		    if ($schools) {
		        $menu[] = ['title' => 'Импорт', 'icon' => 'fa-solid fa-file-import', 'route' => 'import.index', 'pattern' => 'import.*'];
		    }
		    if ($admin) {
		        $menu[] = ['title' => 'Уведомления', 'icon' => 'fa fa-gears', 'route' => 'settings.notifications', 'pattern' => 'settings.notifications'];
		        $menu[] = ['title' => 'Письма перед началом практики', 'icon' => 'fa fa-calendar', 'route' => 'settings.early', 'pattern' => 'settings.early'];
		    }
		}
	@endphp
	<ul class="nav-main">
		@foreach ($menu as $item)
			@if (isset($item['heading']))
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
