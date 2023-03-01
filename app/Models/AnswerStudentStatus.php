<?php

namespace App\Models;

enum AnswerStudentStatus: int {
	case NEW = 0;
	case INVITED = 1;
	case REJECTED = 2;
	case APPROVED = 3;

	public static function getName(int $as): string {
		return match ($as) {
			self::NEW ->value => 'Новый практикант',
			self::INVITED->value => 'Предложен работодателю',
			self::REJECTED->value => 'Отклонен работодателем',
			self::APPROVED->value => 'Одобрен работодателем',
			default => 'Неизвестный статус объекта'
		};
	} //
}