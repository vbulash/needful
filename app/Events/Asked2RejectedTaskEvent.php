<?php

namespace App\Events;

use App\Models\History;
use App\Models\Student;

class Asked2RejectedTaskEvent extends TaskEvent
{
	public function __construct(History $history, Student $student)
	{
		$context = [
			'employer' => $history->timetable->internship->employer->getKey(),
			'internship' => $history->timetable->internship->getKey(),
			'timetable' => $history->timetable->getKey(),
		];
		parent::__construct(
			title: 'Вам необходимо рассмотреть отрицательный ответ кандидата на приглашение на практику',
			description: sprintf(<<<EOD
<h5>Кандидат &laquo;%s&raquo; отказался от участия в практике:</h5>
<p>Работодатель: <strong>%s</strong><br/>
Стажировка: <strong>%s</strong><br/>
%s
График стажировки: <strong>%s</strong></p>
<p>Необходимо отменить приглашение кандидата к участию в стажировке:</p>
<div class='d-flex mb-5'>
	<button class='btn btn-primary me-4' type='event' data-event-type=%d data-history=%d data-student=%d>
		Отменить приглашение</button>
</div>
<p>Вы также можете проигнорировать данное сообщение, в этом случае предложение стажировки автоматически отменится через 10 дней.</p>
<p>Более подробно вы сможете изучить данную информацию по ссылке ниже &darr;</p>
EOD,
				$student->getTitle(), $history->timetable->internship->employer->getTitle(), $history->timetable->internship->getTitle(),
				(isset($history->timetable->internship->short) ? 'Краткая информация по стажировке:<br/><strong>' . $history->timetable->internship->short . '</strong><br/>' : ''),
				$history->timetable->getTitle(),
				EventType::CANCEL_REJECT->value, $history->getKey(), $student->getKey(),
			),
			route: route('timetables.show', ['timetable' => $history->timetable->getKey()]),
			from: $student->user,
			to: $history->timetable->internship->employer->user,
			context: $context,
			script: null
		);
	}
}
