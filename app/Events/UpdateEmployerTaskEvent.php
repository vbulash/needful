<?php

namespace App\Events;

use App\Models\Employer;
use App\Models\User;

/**
 * @property string $title Заголовок задачи
 * @property string $description Описание задачи
 * @property string $url URL задачи
 * @property User $from    Пользователь, сгенерировавший задачу
 * @property string $to Электронная почта получателя задачи
 * @property string $type Тип задачи
 */
class UpdateEmployerTaskEvent extends TaskEvent
{

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Employer $employer)
	{
		parent::__construct(
			title: 'Анкета работодателя проверена',
			description: "Анкета работодателя &laquo;{$employer->getTitle()}&raquo; проверена. Администратор установил данной анкете статус &laquo;Актуальная&raquo;.<br/>" .
			"Теперь данная анкета может использоваться в платформе без ограничений",
			route: route('employers.edit', ['employer' => $employer->getKey()]),
			from: auth()->user(),
			to: $employer->user,
			context: null,
			script: null,
		);
	}
}
