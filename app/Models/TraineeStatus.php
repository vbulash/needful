<?php

namespace App\Models;

enum TraineeStatus: int
{
	case NEW = 0;
	case ASKED = 1;
	case ACCEPTED = 2;
	case REJECTED = 3;
	case CANCELLED = 4;

	public static function getName(int $hs): string
	{
		return match($hs) {
			self::NEW->value => 'Новая',
			self::ASKED->value => 'Запрошено',
			self::ACCEPTED->value => 'Одобрено',
			self::REJECTED->value => 'Отвергнуто',
			self::CANCELLED->value => 'Отменено',
			default => 'Неизвестный статус запроса практиканту'
		};
	}
}
