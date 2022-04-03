<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

	protected $fillable = [
		'iname',
		'itype',
		'status',
		'program',
		'employer_id'
	];

	public function employer() {
		return $this->belongsTo(Employer::class);
	}
}
