<?php

namespace App\Events;

use App\Models\Student;
use App\Models\User;

/**
 * @property string $title Заголовок задачи
 * @property string $description Описание задачи
 * @property string $url URL задачи
 * @property User $from	Пользователь, сгенерировавший задачу
 * @property string $to Электронная почта получателя задачи
 * @property string $type Тип задачи
 */
class UpdateStudentTaskEvent extends TaskEvent
{
	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Student $student)
	{
		parent::__construct(
			title: 'Анкета учащегося проверена',
			description: "Анкета учащегося &laquo;{$student->getTitle()}&raquo; проверена. Администратор установил данной анкете статус &laquo;Актуальная&raquo;.<br/>" .
			"Теперь данная анкета может использоваться в платформе без ограничений",
			route: route('students.edit', ['student' => $student->getKey()]),
			from: auth()->user(),
			to: $student->user,
			context: null,
			script: null,
		);
	}
}
