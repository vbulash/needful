<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
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
            'number' => ['required'],
			'sealed' => ['required', 'date'],
        ];
    }

	public function attributes()
	{
		return [
			'number' => 'Номер договора',
			'sealed' => 'Дата подписания договора',
		];
	}
}
