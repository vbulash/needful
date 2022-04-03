<?php

namespace App\Models;

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

	public function internship() {
		return $this->belongsTo(Internship::class);
	}
}
