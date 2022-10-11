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

	public static function allowed(int $from, int $to): bool
	{
		$states = collect([
			self::NEW->value => [self::ASKED->value],
			self::ASKED->value => [self::ACCEPTED->value, self::REJECTED->value, self::APPROVED->value, self::CANCELLED->value, self::DESTROYED->value],
			self::ACCEPTED->value => [self::APPROVED->value, self::CANCELLED->value, self::DESTROYED->value],
			self::APPROVED->value => [self::DESTROYED->value],
		]);

		return in_array($to, $states->get($from) ?? []);
	}

	public static function getAdminButtons(): array
	{
		return [
			['to' => self::ASKED->value, 'title' => 'Пригласить кандидата', 'icon' => 'fas fa-envelope', 'callback' => 'invite'],
//			['to' => self::ACCEPTED->value, 'title' => 'Одобрение со стороны кандидата', 'icon' => 'fas fa-user-plus', 'callback' => 'accept'],
//			['to' => self::REJECTED->value, 'title' => 'Отказ со стороны кандидата', 'icon' => 'fas fa-user-minus', 'callback' => 'reject'],
			['to' => self::APPROVED->value, 'title' => 'Одобрить кандидата', 'icon' => 'fas fa-check', 'callback' => 'approve'],
			['to' => self::CANCELLED->value, 'title' => 'Отменить приглашение', 'icon' => 'fa-solid fa-ban', 'callback' => 'cancel']
		];
	}

	public static function getButtons(int $from): array|bool
	{
		$states = [
			self::NEW->value => [self::ASKED->value => 'Кандидат интересен'],
			self::ASKED->value => [self::CANCELLED->value => 'Отменить приглашение'],
			self::ACCEPTED->value => [self::APPROVED->value => 'Одобрить кандидата', self::CANCELLED->value => 'Отменить приглашение'],
		];

		return $states[$from] ?? false;
	}
}
