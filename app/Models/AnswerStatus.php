<?php

namespace App\Models;

enum AnswerStatus: int {
	case NEW = 0;
	case FIXED = 1;
	case NAMES = 2;

	public static function getName(int $as): string {
		return match ($as) {
			self::NEW ->value => 'Новый',
			self::FIXED->value => 'Зафиксирован',
			self::NAMES->value => 'Обсуждение практикантов',
			default => 'Неизвестный статус объекта'
		};
	}
}