<?php

namespace App\Events;

use App\Models\School;
use App\Models\User;

/**
 * @property string $title Заголовок задачи
 * @property string $description Описание задачи
 * @property string $url URL задачи
 * @property User $from	Пользователь, сгенерировавший задачу
 * @property string $to Электронная почта получателя задачи
 * @property string $type Тип задачи
 */
class UpdateSchoolTaskEvent extends TaskEvent
{
	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(School $school)
	{
		parent::__construct(
			title: 'Анкета учебного заведения проверена',
			description: "Анкета учебного заведения &laquo;{$school->getTitle()}&raquo; проверена. Администратор установил данной анкете статус &laquo;Актуальная&raquo;.<br/>" .
			"Теперь данная анкета может использоваться в платформе без ограничений",
			route: route('schools.edit', ['school' => $school->getKey()]),
			from: auth()->user(),
			to: $school->user,
			context: null,
			script: null,
		);
	}
}
