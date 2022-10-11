<?php

namespace App\Http\Requests;

use App\Rules\PlanFactTraineesRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHistoryRequest extends FormRequest
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
            'status' => [new PlanFactTraineesRule($this->history)]
        ];
    }

	public function attributes(): array
	{
		return [
			'status' => 'Статус'
		];
	}
}