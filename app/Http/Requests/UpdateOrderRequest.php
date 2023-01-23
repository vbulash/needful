<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
			'name' => 'required',
			'start' => 'required',
			'end' => 'required',
			'place' => 'required',
        ];
    }

	public function attributes() {
		return [
			'name' => 'Название практики',
			'start' => 'Дата начала',
			'end' => 'Дата завершения',
			'place' => 'Населённый пункт прохождения практики',
		];
	}
}
