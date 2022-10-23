<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @property string $title Заголовок задачи
 * @property string $description Описание задачи
 * @property string $route Маршрут объекта задачи
 * @property User $from	Пользователь, сгенерировавший задачу
 * @property string $to Электронная почта получателя задачи
 * @property string $type Тип задачи
 * @property array $context Сессионный контекст ссылки
 * @property string $script Скрипт задачи
 */
abstract class TaskEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public string $title;
	public string $description;
	public ?string $route;
	public ?User $from;
	public ?User $to;
	public int $type;
	public ?array $context;
	public ?string $script;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $title, string $description, ?string $route,
								?User $from, ?User $to, ?array $context, ?string $script, int $type = 0)
    {
		$this->title = $title;
        $this->description = $description;
		$this->route = $route;
		$this->from = $from;
		$this->to = $to;
		$this->context = $context;
		$this->script = $script;
		$this->type = $type;
    }
}
