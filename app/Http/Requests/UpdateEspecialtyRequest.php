<?php

namespace App\Http\Requests;

use App\Rules\OneSpecialtyOnInternshipRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEspecialtyRequest extends FormRequest
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
			'count' => 'Количество позиций'
		];
	}
}
