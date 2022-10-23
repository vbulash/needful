<?php

namespace App\Models;

enum HistoryStatus: int
{
	case NEW = 0;
	case PLANNED = 1;
	case ACTIVE = 2;
	case CLOSED = 3;
	case DESTROYED = 4;

	public static function getName(int $hs): string
	{
		return match($hs) {
			self::NEW->value => 'Новая',
			self::PLANNED->value => 'Запланирована',
			self::ACTIVE->value => 'Выполняется',
			self::CLOSED->value => 'Закрыта',
			self::DESTROYED->value => 'Отменена',
			default => 'Неизвестный статус практики'
		};
	}
}
