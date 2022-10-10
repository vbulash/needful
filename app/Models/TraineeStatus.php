<?php

namespace App\Models;

enum TraineeStatus: int
{
	case NEW = 0;
	case ASKED = 1;
	case ACCEPTED = 2;
	case REJECTED = 3;
	case APPROVED = 4;
	case CANCELLED = 5;
	case DESTROYED = 6;

	public static function getName(int $hs): string
	{
		return match($hs) {
			self::NEW->value => 'Новое приглашение',
			self::ASKED->value => 'Кандидат интересен',
			self::ACCEPTED->value => 'Кандидат подтвердил',
			self::REJECTED->value => 'Кандидат отказался',
			self::APPROVED->value => 'Кандидат утвержден',
			self::CANCELLED->value => 'Приглашение отменено',
			self::DESTROYED->value => 'Стажировка отменена',
			default => 'Неизвестный статус запроса практиканту'
		};
	}
}
