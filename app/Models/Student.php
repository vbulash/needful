<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class Student extends Model implements FormTemplate
{
	use HasFactory, HasTitle;

	protected $fillable = [
		'lastname',
		'firstname',
		'surname',
		'sex',
		'birthdate',
		'phone',
		'email',
		'parents',
		'parentscontact',
		'passport',
		'address',
		'institutions',
		'grade',
		'hobby',
		'hobbyyears',
		'contestachievements',
		'dream',
		'documents',
		'user_id'
	];

	// Геттеры Laravel
	private static function convert2Date($value): DateTime
	{
		if($value instanceof DateTime)
			return $value;
		else {
			$temp = new DateTime($value);
			return $temp;
		}
	}

	protected function birthdate(): Attribute
	{
		return Attribute::make(
			get: fn($value) => self::convert2Date($value),
			set: fn($value) => self::convert2Date($value),
		);

	}

	public function getTitle(): string
	{
		return sprintf("%s %s%s",
			$this->lastname, $this->firstname, $this->surname ? ' ' . $this->surname : '');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function histories() {
		return $this->hasMany(History::class);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'student-create',
			'name' => 'student-create',
			'action' => route('students.store', ['sid' => session()->getId()]),
			'close' => route('students.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'student-edit',
			'name' => 'student-edit',
			'action' => route('students.update', ['student' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('students.index', ['sid' => session()->getId()]),
		];
	}
}
