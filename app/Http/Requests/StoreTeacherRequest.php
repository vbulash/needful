<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
	{
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
	{
        return [
            'name' => 'required',
			'position' => 'required',
			'phone' => 'required',
			'email' => ['required', 'email'],
        ];
    }

	public function attributes(): array
	{
		return [
			'name' => 'ФИО руководителя практики',
			'position' => 'Должность руководителя практики',
			'phone' => 'Телефон',
			'email' => 'Электронная почта',
		];
	}
}
