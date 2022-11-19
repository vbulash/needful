<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEarlyWarningsRequest extends FormRequest {
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
			'cancel' => ['required'],
			'last' => ['required']
		];
	}

	public function attributes(): array {
		return [
			'cancel' => 'Письмо работодателю о последней возможности отмены практики',
			'last' => 'Письмо-предупреждение работодателю и практикантам о начале практики'
		];
	}
}
