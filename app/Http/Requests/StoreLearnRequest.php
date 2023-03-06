<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLearnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules() {
		return [
			'start' => ['required', 'date'],
			'finish' => ['nullable', 'after:start'],
			'school_id' => ['required_without:new_school'],
			'new_school' => ['required_without:school_id'],
			'specialty_id' => ['required_without:new_specialty'],
			'new_specialty' => ['required_without:specialty_id'],
		];
	}

	public function attributes() {
		return [
			'start' => 'Дата поступления',
			'finish' => 'Дата завершения',
			'school_id' => 'Образовательное учреждение',
			'new_school' => 'Новое образовательное учреждение (нет в списке)',
			'specialty_id' => 'Специальность',
			'new_specialty' => 'Новая специальность (нет в списке)',
		];
	}
}