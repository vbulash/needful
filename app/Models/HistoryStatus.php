<?php

namespace App\Models;

enum HistoryStatus: int
{
	case NEW = 0;
	case PLANNED = 1;
	case ACTIVE = 2;
	case CLOSED = 3;

	public static function getName(int $hs): string
	{
		return match($hs) {
			self::NEW->value => 'Новая',
			self::PLANNED->value => 'Запланирована',
			self::ACTIVE->value => 'Выполняется',
			self::CLOSED->value => 'Закрыта',
			default => 'Неизвестный статус практики'
		};
	}
}
