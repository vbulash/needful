<?php

namespace App\Models;

enum AnswerStatus: int
{
	case NEW = 0;
	case FIXED = 1;

	public static function getName(int $as): string
	{
		return match($as) {
			self::NEW->value => 'Новый',
			self::FIXED->value => 'Зафиксирован',
			default => 'Неизвестный статус объекта'
		};
	}
}
