<?php

namespace App\Http\Requests;

use App\Http\Controllers\Auth\RoleName;
use App\Rules\SpecialUserRule;
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
			'user_id' => [new SpecialUserRule($this)],
        ];
//		if(auth()->user()->hasRole(RoleName::ADMIN->value)) $local['user_id'] = ['required'];

		return $local;
    }

	public function attributes()
	{
		$local = [
			'lastname' => 'Фамилия',
			'firstname' => 'Имя',
			'birthdate' => 'Дата рождения',
			'phone' => 'Телефон',
			'email' => 'Электронная почта',
			'user_id' => 'Связанный пользователь'
		];
//		if(auth()->user()->hasRole(RoleName::ADMIN->value)) $local['user_id'] = 'Связанный пользователь';

		return $local;
	}
}
