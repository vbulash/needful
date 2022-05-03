<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model implements FormTemplate
{
    use HasFactory;

	protected $fillable = [
		'start',
		'end',
		'name',
		'internship_id'
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

	protected function start(): Attribute
	{
		return Attribute::make(
			get: fn($value) => self::convert2Date($value),
			set: fn($value) => self::convert2Date($value),
		);

	}

	protected function end(): Attribute
	{
		return Attribute::make(
			get: fn($value) => self::convert2Date($value),
			set: fn($value) => self::convert2Date($value),
		);
	}

	public function getTitle(): string
	{
		return sprintf("С %s по %s", $this->start->format('d.m.Y'), $this->end->format('d.m.Y'));
	}

	public function internship() {
		return $this->belongsTo(Internship::class);
	}

	public function histories() {
		return $this->hasMany(History::class);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'timetable-create',
			'name' => 'timetable-create',
			'action' => route('timetables.store', ['sid' => session()->getId()]),
			'close' => route('timetables.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'timetable-edit',
			'name' => 'timetable-edit',
			'action' => route('timetables.update', ['timetable' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('timetables.index', ['sid' => session()->getId()]),
		];
	}
}
