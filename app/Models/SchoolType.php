<?php

namespace App\Models;

// Тип учебного заведения
enum SchoolType: int
{
	case SCHOOL = 0;		// Школа
	case COLLEGE = 1;		// Среднее учебное заведение
	case UNIVERSITY = 2;	// Высшее учебное заведение

	public static function getName(int $st): string
	{
		return match($st) {
			self::SCHOOL->value => 'Школа',
			self::COLLEGE->value => 'Среднее учебное заведение',
			self::UNIVERSITY->value => 'Высшее учебное заведение',
			default => 'Неизвестный тип учебного заведения'
		};
	}
}
