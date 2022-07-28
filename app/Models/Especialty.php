<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialty extends Model
{
	use HasFactory, HasTitle;

	protected $fillable = [
		'specialty_id',
		'count'
	];

	public static function createTemplate(): array
	{
		return [
			'id' => 'especialty-create',
			'name' => 'especialty-create',
			'action' => route('especialties.store', ['sid' => session()->getId()]),
			'close' => route('especialties.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'especialty-edit',
			'name' => 'especialty-edit',
			'action' => route('especialties.update', ['especialty' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('especialties.index', ['sid' => session()->getId()]),
		];
	}

	public function getTitle(): string
	{
		return $this->specialty->name;
	}

	public function internship() {
		return $this->belongsTo(Internship::class);
	}

	public function specialty() {
		return $this->belongsTo(Specialty::class);
	}
}
