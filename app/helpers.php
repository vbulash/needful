<?php

use App\Models\Answer;
use App\Models\Employer;
use App\Models\EmployerSpecialty;
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

if (!function_exists('form')) {
	function form($formTemplate, int $mode, string $param): string {
		return match ($mode) {
			config('global.create') => $formTemplate::createTemplate()[$param],
			config('global.edit'), config('global.show') => $formTemplate->editTemplate()[$param],
			default => '',
		};
	}
}

if (!function_exists('classByContext')) {
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
			'employer.specialty' => EmployerSpecialty::class,
			'answer' => Answer::class,
			default => null
		};
	}
}

if (!function_exists('createDropdown')) {
	function createDropdown(string $title, array $items) {
		$out = '';
		if (count($items) == 0)
			return '';
		if (count($items) == 1) {
			$item = $items[0];
			if (isset($item['click'])) {
				return sprintf(<<<'EOT'
<a href="javascript:void(0)" onclick="%s" class="btn btn-primary btn-sm float-left ms-1">
	<i class="%s"></i> %s
</a>
EOT, $item['click'], $item['icon'] ?? "fas fa-circle", $item['title']);
			} else {
				return sprintf(<<<'EOT'
<a href="%s" class="btn btn-primary btn-sm float-left ms-1">
	<i class="%s"></i> %s
</a>
EOT, $item['link'], $item['icon'] ?? "fas fa-circle", $item['title']);
			}
		} else {
			foreach ($items as $item) {
				if ($item['type'] == 'divider')
					$out .= "<div class=\"dropdown-divider\"></div>\n";
				elseif (isset($item['click']))
					$out .= sprintf("<li><a class=\"dropdown-item\" href=\"javascript:void(0)\" onclick=\"%s\"><i class=\"%s\"></i> %s</a></li>\n",
						$item['click'], $item['icon'] ?? "fas fa-circle", $item['title']);
				else
					$out .= sprintf("<li><a class=\"dropdown-item\" href=\"%s\"><i class=\"%s\"></i> %s</a></li>\n",
						$item['link'], $item['icon'] ?? "fas fa-circle", $item['title']);
			}
			return sprintf(<<<'EOT'
<div class="dropdown">
	<button type="button" class="btn btn-primary dropdown-toggle show actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		%s
    </button>
	<div class="dropdown-menu" aria-labelledby="dropdown-dropup-primary" data-popper-placement="top-start">
		%s
	</div>
</div>
EOT, $title, $out);
		}
	}
}
