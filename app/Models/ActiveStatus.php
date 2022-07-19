<?php

namespace App\Models;

enum ActiveStatus: int
{
	case NEW = 0;
	case ACTIVE = 1;
	case FROZEN = 2;

	public static function getName(int $as): string
	{
		return match($as) {
			self::NEW->value => 'Новый',
			self::ACTIVE->value => 'Активный',
			self::FROZEN->value => 'Пауза',
			default => 'Неизвестный статус объекта'
		};
	}
}
