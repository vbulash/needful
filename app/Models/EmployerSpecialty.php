<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerSpecialty extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $table = 'employers_specialties';

	public function getTitle(): string {
		return $this->employer->getTitle();
	}

	public function employer() {
		return $this->belongsTo(Employer::class);
	}

	public function specialty() {
		return $this->belongsTo(Specialty::class);
	}

	public static function createTemplate(): array {
		$context = session('context');
		$employer = $context['employer'];
		return [
			'id' => 'employer-specialty-create',
			'name' => 'employer-specialty-create',
			'action' => route('employer.specialties.store',['employer' => $employer]),
			'close' => route('employer.specialties.index', ['employer' => $employer]),
		];
	}

	public function editTemplate(): array {
		$context = session('context');
		$employer = $context['employer'];
		return [
			'id' => 'employer-specialty-edit',
			'name' => 'employer-specialty-edit',
			'action' => route('employer.specialties.update', ['employer' => $employer, 'specialty' => $this->getKey()]),
			'close' => route('employer.specialties.index', ['employer' => $employer]),
		];
	}
}
