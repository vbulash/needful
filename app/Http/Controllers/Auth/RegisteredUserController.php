<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewUser;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class RegisteredUserController extends Controller
{
	/**
	 * Display the registration view.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		$roles = Role::where('selfassign', true)
			->orderBy('name')
			->pluck('name')
			->toArray();
		return view('auth.register', ['roles' => $roles]);
	}

	/**
	 * Handle an incoming registration request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function store(NewUserRequest $request)
	{
		$role = $request->role;
		try {
			$user = User::create([
				'name' => $request->name,
				'email' => $request->email,
				'password' => Hash::make($request->password),
			]);
			$user->assignRole($role);
			$rights = match ($role) {
				RoleName::EMPLOYER->value => ['employers.edit', 'employers.show'],
				RoleName::SCHOOL->value => ['schools.edit', 'schools.show'],
				RoleName::TRAINEE->value => ['students.edit', 'students.show'],
				default => []
			};
			foreach ($rights as $right) {
				$this->addWildcard($user, $right, $user->getKey());
			}

			event(new Registered($user));
			$user->notify(new NewUser($user));
			$name = $user->name;

			auth()->login($user);

			session()->put('success',
				"Зарегистрирован новый пользователь \"{$name}\" с ролью \"{$role}\"");

			if (auth()->user()->hasRole(RoleName::TRAINEE->value)) {
				if (!auth()->user()->students()->count())
					return redirect()->route('students.index');
			} elseif (auth()->user()->hasRole(RoleName::EMPLOYER->value)) {
				if (!auth()->user()->employers()->count())
					return redirect()->route('employers.index');
			} elseif (auth()->user()->hasRole(RoleName::SCHOOL->value)) {
				if (!auth()->user()->schools()->count())
					return redirect()->route('schools.index');
			}
			return redirect()->route('dashboard');
		} catch (Exception $exc) {
			session()->put('error',
				"Ошибка регистрации нового пользователя: {$exc->getMessage()}");

			return redirect()->route('register');
		}
	}

	private function addWildcard(User $user, string $right, int $id)
	{
		if ($user->hasPermissionTo($right)) {
			$permission = "{$right}.{$id}";
			Permission::findOrCreate($permission);
			$user->givePermissionTo($permission);
		}
	}
}
