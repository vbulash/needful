<?php

namespace App\Events;

use App\Models\History;
use App\Models\Student;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class All2DestroyedTaskEvent extends TaskEvent
{
    public function __construct(History $history, Student $student)
	{
		parent::__construct(
			title: 'Практика отменена',
			description: sprintf(<<<EOD
<h5>Уважаемый (уважаемая) %s </h5>
<p>С сожалением сообщаем, что стажировка:</p>
<p>Работодатель: <strong>%s</strong><br/>
Стажировка: <strong>%s</strong><br/>
%s
График стажировки: <strong>%s</strong></p>
<p>отменена и все приглашения кандидатам в практиканты отменены.</p>
<p>Мы надеемся на плодотворное сотрудничество и планируем в дальнейшем предложить вам участие в других стажировках</p>
EOD,
				$student->getTitle(), $history->timetable->internship->employer->getTitle(), $history->timetable->internship->getTitle(),
				(isset($history->timetable->internship->short) ? 'Краткая информация по стажировке:<br/><strong>' . $history->timetable->internship->short . '</strong><br/>' : ''),
				$history->timetable->getTitle(),
			),
			route: null,
			from: $history->timetable->internship->employer->user,
			to: $student->user,
			context: null,
			script: null
		);
	}
}
