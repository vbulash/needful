<?php

namespace App\Models;

enum OrderSpecialtyStatus: int
{
	case CREATED = 0;
	case FILLED = 1;

	public static function getName(int $oss): string
	{
		return match($oss) {
			self::CREATED->value => 'Новая',
			self::FILLED->value => 'Заполнена',
			default => 'Неизвестный статус специальности в заявке на практику'
		};
	}
}
