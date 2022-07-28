<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'iname',
		'itype',
		'status',
		'program',
		'employer_id'
	];

	public function getTitle(): string
	{
		return $this->iname;
	}

	public function employer() {
		return $this->belongsTo(Employer::class);
	}

	public function especialties() {
		return $this->hasMany(Especialty::class);
	}

	public function timetables() {
		return $this->hasMany(Timetable::class);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'internship-create',
			'name' => 'internship-create',
			'action' => route('internships.store', ['sid' => session()->getId()]),
			'close' => route('internships.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'internship-edit',
			'name' => 'internship-edit',
			'action' => route('internships.update', ['internship' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('internships.index', ['sid' => session()->getId()]),
		];
	}
}
