<?php

namespace App\Events;

enum EventType: int
{
	case MESSAGE = 0;
	case INVITE_TRAINEE = 1;
	case TRAINEE_ACCEPTED = 2;
	case TRAINEE_REJECTED = 3;
	case APPROVE_TRAINEE = 4;
	case CANCEL_TRAINEE = 5;
	case CANCEL_REJECT = 6;

	public static function getName(int $hs): string
	{
		return match($hs) {
			self::MESSAGE->value => 'Текстовое сообщение',
			self::INVITE_TRAINEE->value => 'Приглашение на практику',
			self::TRAINEE_ACCEPTED->value => 'Положительный ответ кандидата',
			self::TRAINEE_REJECTED->value => 'Отрицательный ответ кандидата',
			self::APPROVE_TRAINEE->value => 'Утверждение кандидата',
			self::CANCEL_TRAINEE->value => 'Отказ от кандидата',
			self::CANCEL_REJECT->value => ' Отказ кандидата и отмена приглашения',
			default => 'Неизвестный тип события'
		};
	}
}
