@section('menu')
	@php
		global $menu;
		$menu = [['title' => 'Режим &laquo;Перечень образовательных учреждений&raquo;', 'route' => 'schools.index'], ['title' => 'Режим &laquo;Учащиеся образовательных учреждений&raquo;', 'route' => 'dashboard']];
	@endphp
@endsection
