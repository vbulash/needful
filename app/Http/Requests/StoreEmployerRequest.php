<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployerRequest extends FormRequest
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
		// TODO строгая проверка ИНН / ОГРН, идентичная Платформе нейротестирования 1
        return [
			'name' => ['required'],
			'phone' => ['required'],
			'email' => ['email', 'required'],
			'inn' => ['numeric', 'required'],
			'ogrn' => ['numeric', 'required'],
			'post_address' => ['required']
        ];
    }

	public function attributes()
	{
		return [
			'name' => 'Наименование организации',
			'phone' => 'Телефон',
			'email' => 'Электронная почта',
			'inn' => 'Индивидуальный номер налогоплательщика (ИНН)',
			'ogrn' => 'ОГРН / ОГРНИП',
			'post_address' => 'Почтовый адрес'
		];
	}
}
