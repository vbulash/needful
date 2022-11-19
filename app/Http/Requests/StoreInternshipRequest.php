<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternshipRequest extends FormRequest
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
			'iname' => 'required',
			'itype' => 'required',
			'program' => 'required',
        ];
    }

	public function attributes()
	{
		return [
			'iname' => 'Название практики',
			'itype' => 'Тип практики',
			'program' => 'Программа практики'
		];
	}
}
