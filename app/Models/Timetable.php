<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

	protected $fillable = [
		'start',
		'end',
		'name',
		'internship_id'
	];

	public function getTitle(): string
	{
		switch (env('DB_CONNECTION')) {
			case 'sqlite':
				$start = $this->start;
				break;
			case 'mysql':
			default:
				$start = DateTime::createFromFormat('Y-m-d', $this->start);
				$start = $start->format('d.m.Y');
				break;
		}

		switch (env('DB_CONNECTION')) {
			case 'sqlite':
				$end = $this->end;
			case 'mysql':
			default:
				$end = DateTime::createFromFormat('Y-m-d', $this->end);
				$end = $end->format('d.m.Y');
		}
		return sprintf("С %s по %s", $start, $end);
	}

	public function internship() {
		return $this->belongsTo(Internship::class);
	}
}
