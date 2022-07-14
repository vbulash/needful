<?php

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
			'employer' => \App\Models\Employer::class,
			'role' => \App\Models\Role::class,
			'user' => \App\Models\User::class,
			'school' => \App\Models\School::class,
			'specialty' => \App\Models\Specialty::class,
			'fspecialty' => \App\Models\Fspecialty::class,
		};
	}
}
