<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string $route
 * @property bool $fromadmin
 * @property bool $toadmin
 * @property int $type
 * @property bool $read
 * @property string $context
 * @property string script
 * @property bool $archive
 * @method static findOrFail(int $id)
 * @method static where(string $string, false $false)
 */
class Task extends Model
{
    use HasFactory;

	protected $table = 'tasks';
	protected $fillable = [
		'uuid',			// Глобальный идентификатор задачи
		'title',		// Заголовок задачи
		'description',	// Текст описания задачи
		'route',		// Маршрут объекта задачи
		'fromadmin',	// Пользователь-автор задачи = администратор
		'toadmin',		// Пользователь-приёмник задачи = администратор
		'type',			// Тип задачи
		'read',			// Сообщение прочтено (true) или не прочтено (false)
		'context',		// Контекст объекта задачи
		'script',		// Скрипт задачи
		'archive',		// Признак архивной задачи
	];

	public function from(): BelongsTo
	{
		return $this->belongsTo(User::class, 'from_id');
	}

	public function to(): BelongsTo
	{
		return $this->belongsTo(User::class, 'to_id');
	}
}
