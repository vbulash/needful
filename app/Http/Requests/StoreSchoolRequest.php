<?php

namespace App\Http\Requests;

class StoreSchoolRequest extends StoreEmployerRequest
{
	public function attributes()
	{
		$data = parent::attributes();
		$data['name'] = 'Наименование учебного заведения';
		return $data;
	}
}
