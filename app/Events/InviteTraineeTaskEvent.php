<?php

namespace App\Events;

use App\Models\History;
use App\Models\Student;

class InviteTraineeTaskEvent extends TaskEvent
{
	public function __construct(History $history, Student $trainee)
	{
		$context = [
			'employer' => $history->timetable->internship->employer->getKey(),
			'internship' => $history->timetable->internship->getKey(),
			'timetable' => $history->timetable->getKey(),
		];

		$accept = sprintf(<<<EOS
document.getElementById('accept').addEventListener('click', event => {
	$.ajax({
		method: 'POST',
		url: '%s',
		data: {
			history: %d,
			trainee: %d,
			task: window.message
		},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: data => {
			if (data !== undefined) {
				window.location.href = '%s';
			}
		}
	});
}, false);
EOS,
			route('history.accept'), $history->getKey(), $trainee->getKey(), route('inbox.archive'));

		$reject = sprintf(<<<EOS
document.getElementById('reject').addEventListener('click', event => {
	$.ajax({
		method: 'POST',
		url: '%s',
		data: {
			history: %d,
			trainee: %d,
			task: window.message
		},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: data => {
			if (data !== undefined) {
				window.location.href = '%s';
			}
		}
	});
}, false);
EOS,
			route('history.reject'), $history->getKey(), $trainee->getKey(), route('inbox.archive'));

		parent::__construct(
			title: 'Вы приглашены для участия в прохождении практики',
			description: sprintf(<<<EOD
<h5>Уважаемый (уважаемая) %s </h5>
<p>Вас пригласили для прохождения практики. Информация по данной практике:</p>
<p>Работодатель: <strong>%s</strong><br/>
Стажировка: <strong>%s</strong><br/>
%s
График стажировки: <strong>%s</strong></p>
<p>Просим принять решение по стажировке:</p>
<div class='d-flex mb-5'>
	<button class='btn btn-primary me-4' type='event' data-event-type=%d data-button-type='accept' data-history=%d data-trainee=%d>
		Да, принять участие</button>
	<button class='btn btn-secondary' type='event' data-event-type=%d data-button-type='reject' data-history=%d data-trainee=%d>
		Нет, не принимать участие</button>
</div>
<p>Вы также можете проигнорировать данное сообщение, в этом случае предложение стажировки автоматически отменится через 10 дней.</p>
<p>Более подробно вы сможете изучить данную информацию по ссылке ниже &darr;</p>
EOD,
				$trainee->getTitle(), $history->timetable->internship->employer->getTitle(), $history->timetable->internship->getTitle(),
				(isset($history->timetable->internship->short) ? 'Краткая информация по стажировке:<br/><strong>' . $history->timetable->internship->short . '</strong><br/>' : ''),
				$history->timetable->getTitle(),
				EventType::INVITE_ATTENDEES->value, $history->getKey(), $trainee->getKey(),
				EventType::INVITE_ATTENDEES->value, $history->getKey(), $trainee->getKey(),
			),
			route: route('timetables.show', ['timetable' => $history->timetable->getKey()]),
			from: auth()->user(),
			to: $trainee->user,
			context: $context,
			script: null
		);
	}

}
