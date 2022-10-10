<?php

namespace App\Http\Requests;

use App\Http\Controllers\Auth\RoleName;
use App\Rules\ChangeStudentStatusRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
			'status' => [new ChangeStudentStatusRule($this)],
			'lastname' => ['required'],
			'firstname' => ['required'],
			'birthdate' => ['date', 'required', 'before:today'],
			'phone' => ['required'],
			'email' => ['required', 'email'],
		];
		if(auth()->user()->hasRole(RoleName::ADMIN->value)) $local['user_id'] = ['required'];

		return $local;
	}

	public function attributes()
	{
		$local = [
			'status' => 'Статус',
			'lastname' => 'Фамилия',
			'firstname' => 'Имя',
			'birthdate' => 'Дата рождения',
			'phone' => 'Телефон',
			'email' => 'Электронная почта'
		];
		if(auth()->user()->hasRole(RoleName::ADMIN->value)) $local['user_id'] = 'Связанный пользователь';

		return $local;
	}
}
