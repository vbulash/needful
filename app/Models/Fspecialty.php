<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fspecialty extends Model
{
	use HasFactory, HasTitle;

	protected $fillable = [
		'specialty_id'
	];

	public static function createTemplate(): array
	{
		return [
			'id' => 'fspecialty-create',
			'name' => 'fspecialty-create',
			'action' => route('fspecialties.store', ['sid' => session()->getId()]),
			'close' => route('fspecialties.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'fspecialty-edit',
			'name' => 'fspecialty-edit',
			'action' => route('fspecialties.update', ['fspecialty' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('fspecialties.index', ['sid' => session()->getId()]),
		];
	}

	public function getTitle(): string
	{
		return $this->specialty->name;
	}

	public function school() {
		return $this->belongsTo(School::class);
	}

	public function specialty() {
		return $this->belongsTo(Specialty::class);
	}
}
