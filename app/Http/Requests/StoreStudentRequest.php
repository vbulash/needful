<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
		$local = [
            'lastname' => ['required'],
			'firstname' => ['required'],
			//'specialties' => [new SpecialtiesRule()],
			'birthdate' => ['date', 'required', 'before:today'],
			'phone' => ['required'],
			'email' => ['required', 'email'],
        ];
		if(auth()->user()->hasRole('Администратор')) $local['user_id'] = ['required'];

		return $local;
    }

	public function attributes()
	{
		$local = [
			'lastname' => 'Фамилия',
			'firstname' => 'Имя',
			'birthdate' => 'Дата рождения',
			'phone' => 'Телефон',
			'email' => 'Электронная почта'
		];
		if(auth()->user()->hasRole('Администратор')) $local['user_id'] = 'Связанный пользователь';

		return $local;
	}
}
