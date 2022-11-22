<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, GrantedAll;

	public function school() {
		return $this->belongsTo(School::class);
	}

	public function specialties() {
		return $this->hasMany(OrderSpecialty::class, 'orders_specialties');
	}
}
