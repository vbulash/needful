<?php

namespace App\Notifications\e2s;

use App\Models\History;

class HasInternship
{
	public static function getLines(History $history): array
	{
		$lines = [];
		$lines[] = "- **Работодатель**: \"{$history->timetable->internship->employer->getTitle()}\"";
		$lines[] = "- **Практика**: \"{$history->timetable->internship->getTitle()}\"";
		if (isset($history->timetable->internship->short))
			$lines[] = "- **Краткая информация по практике**: \"{$history->timetable->internship->short}\"";
		$lines[] = "- **График практики**: {$history->timetable->getTitle()}";
		return $lines;
	}
}
