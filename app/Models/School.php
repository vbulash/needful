<?php /** @noinspection ALL */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static findOrFail(mixed $school_id)
 */
class School extends Model implements FormTemplate
{
    use HasFactory, HasTitle, GrantedAll;

	protected $fillable = [
		'short',	// Краткое наименование учебного заведения
		'name',		// Наименование учебного заведения
		'type',		// Тип учебного заведения
		'status',	// Статус активности объекта
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
		return $this->short;
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function fspecialties()
	{
		return $this->hasMany(Fspecialty::class);
	}

	public function learns(): HasMany
	{
		return $this->hasMany(Learn::class);
	}

	public function teachers()
	{
		return $this->morphMany(Teacher::class, 'job');
	}

	/**
	 * The "booted" method of the model.
	 *
	 * @return void
	 */
	protected static function booted()
	{
		static::deleting(function ($school) {
			$school->teachers()->detach($school);
		});
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
