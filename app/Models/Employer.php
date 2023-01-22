<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property string $short
 * @property string $name
 * @property int $status
 * @property string contact
 * @property string address
 * @property string phone
 * @property string email
 * @property string inn
 * @property string kpp
 * @property string ogrn
 * @property string official_address
 * @property string post_address
 * @property int $user_id
 *
 * @method static create(array $data)
 * @method static findOrFail(int $id)
 */
class Employer extends Model implements FormTemplate {
	use HasFactory, HasTitle, GrantedAll;

	protected $fillable = [
		'short',
		'name',
		'status',
		'contact',
		'address',
		'phone',
		'email',
		'inn',
		'kpp',
		'ogrn',
		'official_address',
		'post_address',
		'user_id'
	];

	public function getTitle(): string {
		return $this->short;
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function internships() {
		return $this->hasMany(Internship::class);
	}

	public function teachers(): MorphMany {
		return $this->morphMany(Teacher::class, 'job');
	}

	/**
	 * The "booted" method of the model.
	 *
	 * @return void
	 */
	protected static function booted() {
		static::deleting(function ($employer) {
			$employer->teachers()->detach($employer);
		});
	}

	public static function createTemplate(): array {
		return [
			'id' => 'employer-create',
			'name' => 'employer-create',
			'action' => route('employers.store', ['sid' => session()->getId()]),
			'close' => route('employers.index', ['sid' => session()->getId()]),
		];
	}

	public function editTemplate(): array {
		return [
			'id' => 'employer-edit',
			'name' => 'employer-edit',
			'action' => route('employers.update', ['employer' => $this->getKey(), 'sid' => session()->getId()]),
			'close' => route('employers.index', ['sid' => session()->getId()]),
		];
	}

	public function users(): MorphToMany {
		return $this->morphToMany(User::class, 'right');
	}

	public function specialties(): HasMany {
		return $this->hasMany(EmployerSpecialty::class);
	}

	public function answers(): HasMany {
		return $this->hasMany(Answer::class);
	}

	public function orders(): BelongsToMany {
		return $this->belongsToMany(Order::class, 'orders_employers')
			->withTimestamps()
			->withPivot(['status']);
	}
}
