<?php

namespace App\Notifications;

class UpdateContract extends NewContract
{
	protected function getSubject(): string {
		return sprintf("Изменён %sдоговор", $this->empty ? 'рамочный ' : '');
	}
}
