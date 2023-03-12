<?php

namespace App\Models;

enum OrderEmployerStatus: int {
	case NEW = 0;
	case SENT = 1;
	case ACCEPTED = 2;
	case REJECTED = 3;
	case DONE = 4;

	public static function getName(int $oes): string {
		return match ($oes) {
			self::NEW ->value => 'Новое',
			self::SENT->value => 'Переслано работодателю',
			self::ACCEPTED->value => 'Принято работодателем',
			self::REJECTED->value => 'Работодатель отказался',
			self::DONE->value => 'Практиканты согласованы',
			default => 'Неизвестный статус уведомления работодателю в заявке на практику'
		};
	}
}