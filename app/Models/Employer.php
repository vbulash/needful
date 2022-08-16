<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'name',
		'status',
		'contact',
		'address',
		'phone',
		'email',
		'inn',
		'kpp',
		'ogrn',
		'official_address',
		'post_address',
		'user_id'
	];

	public function getTitle(): string
	{
		return $this->name;
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function internships()
	{
		return $this->hasMany(Internship::class);
	}

	public function teachers()
	{
		return $this->morphMany(Teacher::class, 'job');
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'employer-create',
			'name' => 'employer-create',
			'action' => route('employers.store', ['sid' => session()->getId()]),
			'close' => route('employers.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'employer-edit',
			'name' => 'employer-edit',
			'action' => route('employers.update', ['employer' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('employers.index', ['sid' => session()->getId()]),
		];
	}
}
