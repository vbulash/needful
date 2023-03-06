<?php

namespace App\Http\Requests;

class StoreSchoolRequest extends StoreEmployerRequest {
	public function attributes() {
		$data = parent::attributes();
		$data['short'] = 'Краткое наименование образовательного учреждения';
		$data['name'] = 'Наименование образовательного учреждения';
		return $data;
	}
}