<?php

namespace App\Models;

use App\Http\Controllers\Auth\RoleName;

trait Right
{

	public function allow(object $element): void
	{
		if ($this->hasRole(RoleName::ADMIN->value)) return;

		$user = $this;
		$attached = false;
		if (!$user->allowed($element)->find($element->getKey())) {
			$user->allowed($element)->attach($element);
			$attached = true;
		}
		if ($attached) $user->save();
	}

	public function disallow(object $element): void
	{
		if ($this->hasRole(RoleName::ADMIN->value)) return;

		$user = $this;
		$detached = false;
		if ($user->allowed($element)->find($element->getKey())) {
			$user->allowed($element)->detach($element);
			$detached = true;
		}
		if ($detached) $user->save();
	}

	public function isAllowed(object $element): bool
	{
		if ($this->hasRole(RoleName::ADMIN->value)) return true;
		$user = $this;
		return $user->allowed($element)->find($element->getKey()) != null;
	}

	public function getAllowed(string $class): array
	{
		$user = $this;
		return $user->allowed($class)->getResults()->pluck('id')->toArray();
	}
}
