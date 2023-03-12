<?php

namespace App\Models;

enum AnswerStatus: int {
	case NEW = 0;
	case ACCEPTED = 1;
	case REJECTED = 2;
	case NAMES = 3;
	case DONE = 4;

	public static function getName(int $as): string {
		return match ($as) {
			self::NEW ->value => 'Новый',
			self::ACCEPTED->value => 'Предложение принято',
			self::REJECTED->value => 'Предложение отклонено',
			self::NAMES->value => 'Обсуждение практикантов',
			self::DONE->value => 'Практиканты согласованы',
			default => 'Неизвестный статус объекта'
		};
	}
}