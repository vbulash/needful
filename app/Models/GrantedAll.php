<?php

namespace App\Models;

use App\Http\Controllers\Auth\RoleName;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

trait GrantedAll {
	/**
	 * @param  array|string  $columns
	 * @return Collection<int, static>
	 */
	public static function all($columns = ['*']): Collection {
		if (!Auth::check())
			return parent::all();

		if (auth()->user()->hasRole(RoleName::ADMIN->value))
			return parent::all();

		$allowed = auth()->user()->getAllowed(self::class);
		if (count($allowed) == 0)
			return parent::all();
		else
			return parent::all()->whereIn('id', $allowed);
	}

	public static function fullAll($columns = ['*']): Collection {
		return parent::all();
	}
}