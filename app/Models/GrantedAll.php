<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;

trait GrantedAll
{
	/**
	 * Get all the models from the database.
	 *
	 * @param  array|string  $columns
	 * @return Collection<int, static>
	 */
	public static function all($columns = ['*']): Collection
	{
		$allowed = auth()->user()->getAllowed(self::class);
		if (count($allowed) == 0) return parent::all();
		else return parent::all()->whereIn('id', $allowed);
	}
}
