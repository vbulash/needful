<?php

namespace App\Http\Requests;

class StoreSchoolRequest extends StoreEmployerRequest
{
	public function attributes()
	{
		$data = parent::attributes();
		$data['short'] = 'Краткое наименование учебного заведения';
		$data['name'] = 'Наименование учебного заведения';
		return $data;
	}
}
