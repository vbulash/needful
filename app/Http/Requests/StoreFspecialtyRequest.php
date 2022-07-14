<?php

namespace App\Http\Requests;

use App\Rules\OneSpecialtyOnSchoolRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFspecialtyRequest extends FormRequest
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
				new OneSpecialtyOnSchoolRule($this)
			],
            'specialty' => [
				'nullable',
				'unique:specialties,name'
			]
        ];
    }

	public function attributes()
	{
		return [
			'specialty_id' => 'Выбор специальности',
			'specialty' => 'Новая специальность',
		];
	}
}
