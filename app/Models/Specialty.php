<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model implements FormTemplate
{
    use HasFactory;

	protected $fillable = [
		'federal',	// Признак федерального справочника профессий
		'order',	// Номер по порядку в федеральном справочнике
		'code',		// Код из федерального справочника
		'name',		// Название специальности
		'degree',	// Квалификация по федеральному справочнику
	];

	public function level0()
	{
		return $this->belongsTo(Item::class, 'level0_id');
	}

	public function level1()
	{
		return $this->belongsTo(Item::class, 'level1_id');
	}

	public function level2()
	{
		return $this->belongsTo(Item::class, 'level2_id');
	}

	public function students() {
		return $this->belongsToMany(Student::class, 'student_specialty')->withTimestamps();
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'specialty-create',
			'name' => 'specialty-create',
			'action' => route('specialties.store', ['sid' => session()->getId()]),
			'close' => route('specialties.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'specialty-edit',
			'name' => 'specialty-edit',
			'action' => route('specialties.update', ['specialty' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('specialties.index', ['sid' => session()->getId()]),
		];
	}

	public function fspecialties() {
		return $this->hasMany(Fspecialty::class);
	}
}
