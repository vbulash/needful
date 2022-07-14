<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model implements FormTemplate
{
    use HasFactory, HasTitle;

	protected $fillable = [
		'name',		// Наименование учебного заведения
		'type',		// Тип учебного заведения
		'contact',	// Контактное лицо
		'phone',	// Телефон
		'email',	// Электронная почта
		'inn',		// ИНН
		'kpp',		// КПП
		'ogrn',		// ОГРН / ОГРНИП
		'official_address',	// Юридический адрес
		'post_address',		// Почтовый адрес
		//
		'user_id',	// Связанный пользователь
	];

	public function getTitle(): string
	{
		return $this->name;
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function fspecialties()
	{
		return $this->hasMany(Fspecialty::class);
	}

	public static function createTemplate(): array
	{
		return [
			'id' => 'school-create',
			'name' => 'school-create',
			'action' => route('schools.store', ['sid' => session()->getId()]),
			'close' => route('schools.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array
	{
		return [
			'id' => 'school-edit',
			'name' => 'school-edit',
			'action' => route('schools.update', ['school' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('schools.index', ['sid' => session()->getId()]),
		];
	}
}
