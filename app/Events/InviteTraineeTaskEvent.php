<?php

namespace App\Events;

use App\Models\History;
use App\Models\Student;

class InviteTraineeTaskEvent extends TaskEvent
{
	public function __construct(History $history, Student $student)
	{
		$context = [
			'employer' => $history->timetable->internship->employer->getKey(),
			'internship' => $history->timetable->internship->getKey(),
			'timetable' => $history->timetable->getKey(),
		];

		parent::__construct(
			title: 'Вы приглашены для участия в прохождении практики',
			description: sprintf(<<<EOD
<h5>Уважаемый (уважаемая) %s </h5>
<p>Вас пригласили для прохождения практики. Информация по данной практике:</p>
<p>Работодатель: <strong>%s</strong><br/>
Практика: <strong>%s</strong><br/>
%s
График практики: <strong>%s</strong></p>
<p>Просим принять решение по практике:</p>
<div class='d-flex mb-5'>
	<button class='btn btn-primary me-4' type='event' data-event-type=%d data-history=%d data-student=%d>
		Да, принять участие</button>
	<button class='btn btn-secondary' type='event' data-event-type=%d data-history=%d data-student=%d>
		Нет, не принимать участие</button>
</div>
<p>Вы также можете проигнорировать данное сообщение, в этом случае предложение практики автоматически отменится через 10 дней.</p>
<p>Более подробно вы сможете изучить данную информацию по ссылке ниже &darr;</p>
EOD,
				$student->getTitle(), $history->timetable->internship->employer->getTitle(), $history->timetable->internship->getTitle(),
				(isset($history->timetable->internship->short) ? 'Краткая информация по практике:<br/><strong>' . $history->timetable->internship->short . '</strong><br/>' : ''),
				$history->timetable->getTitle(),
				EventType::TRAINEE_ACCEPTED->value, $history->getKey(), $student->getKey(),
				EventType::TRAINEE_REJECTED->value, $history->getKey(), $student->getKey(),
			),
			route: route('timetables.show', ['timetable' => $history->timetable->getKey()]),
			from: $history->timetable->internship->employer->user,
			to: $student->user,
			context: $context,
			script: null
		);
	}
}
