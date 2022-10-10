<?php

namespace App\Events;

enum EventType: int
{
	case MESSAGE = 0;
	case INVITE_ATTENDEES = 1;
	case ATTENDEE_ACCEPTED = 2;
	case ATTENDEE_REJECTED = 3;

	public static function getName(int $hs): string
	{
		return match($hs) {
			self::MESSAGE->value => 'Текстовое сообщение',
			self::INVITE_ATTENDEES->value => 'Приглашение на практику',
			self::ATTENDEE_ACCEPTED->value => 'Положительный ответ кандидата',
			self::ATTENDEE_REJECTED->value => 'Отрицательный ответ кандидата',
			default => 'Неизвестный тип события'
		};
	}
}
