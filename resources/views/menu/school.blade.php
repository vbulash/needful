@section('menu')
	@php
		global $menu;
		$menu = [
            ['title' => "Режим &laquo;Перечень учебных заведений&raquo;", 'route' => 'schools.index'],
            ['title' => "Режим &laquo;Учащиеся учебных заведений&raquo;", 'route' => 'dashboard'],
		];
	@endphp
@endsection
