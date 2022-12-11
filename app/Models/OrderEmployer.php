<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEmployer extends Model
{
    use HasFactory;

	protected $fillable = [
		'status',	// Статус информирования работодателя
	];

	public function order(): BelongsTo {
		return $this->belongsTo(Order::class);
	}

	public function employer(): BelongsTo {
		return $this->belongsTo(Employer::class);
	}
}
