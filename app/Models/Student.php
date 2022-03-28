<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
	use HasFactory;

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

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
