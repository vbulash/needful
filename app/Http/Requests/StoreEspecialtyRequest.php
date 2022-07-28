<?php

namespace App\Http\Requests;

use App\Rules\OneSpecialtyOnInternshipRule;
use App\Rules\OneSpecialtyOnSchoolRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEspecialtyRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'specialty_id' => [
				new OneSpecialtyOnInternshipRule($this)
			],
			'specialty' => [
				'nullable',
				'unique:specialties,name'
			],
			'count' => [
				'required',
				'numeric'
			]
		];
	}

	public function attributes()
	{
		return [
			'specialty_id' => 'Выбор специальности',
			'specialty' => 'Новая специальность',
			'count' => 'Количество позиций'
		];
	}
}
