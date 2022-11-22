<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSpecialty extends Model
{
    use HasFactory;

	protected $table = 'orders_specialties';
	protected $fillable = [
		'quantity',	// Количество позиций по специальности
	];

	public function order() {
		return $this->belongsTo(Order::class);
	}
}
