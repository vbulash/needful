<?php

use App\Models\Employer;
use App\Models\Especialty;
use App\Models\Fspecialty;
use App\Models\History;
use App\Models\Internship;
use App\Models\Learn;
use App\Models\Order;
use App\Models\OrderEmployer;
use App\Models\OrderSpecialty;
use App\Models\Role;
use App\Models\School;
use App\Models\Specialty;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\User;

if (! function_exists('form')) {
	function form($formTemplate, int $mode, string $param): string {
		return match ($mode) {
			config('global.create') => $formTemplate::createTemplate()[$param],
			config('global.edit'), config('global.show') => $formTemplate->editTemplate()[$param],
			default => '',
		};
	}
}

if (! function_exists('classByContext')) {
	function classByContext(string $context) {
		return match ($context) {
			'employer' => Employer::class,
			'internship' => Internship::class,
			'learn' => Learn::class,
			'role' => Role::class,
			'user' => User::class,
			'school' => School::class,
			'specialty' => Specialty::class,
			'student', 'trainee' => Student::class,
			'fspecialty' => Fspecialty::class,
			'especialty' => Especialty::class,
			'teacher' => Teacher::class,
			'timetable' => Timetable::class,
			'history' => History::class,
			'order' => Order::class,
			'order.specialty' => OrderSpecialty::class,
			'order.employer' => OrderEmployer::class,
			default => null
		};
	}
}
