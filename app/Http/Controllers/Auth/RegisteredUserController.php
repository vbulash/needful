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

			event(new Registered($user));
			$user->notify(new NewUser($user));
			$name = $user->name;

			auth()->login($user);

			session()->put('success',
				"Зарегистрирован новый пользователь \"{$name}\" с ролью \"{$role}\"");

				if (auth()->user()->hasRole(RoleName::TRAINEE->value)) {
					if (auth()->user()->students()->count() == 0)
					return redirect()->route('students.create', ['for' => auth()->user()->email]);
				} elseif (auth()->user()->hasRole(RoleName::EMPLOYER->value)) {
					if (auth()->user()->employers()->count() == 0)
						return redirect()->route('employers.create');
				} elseif (auth()->user()->hasRole(RoleName::SCHOOL->value)) {
					if (auth()->user()->schools()->count() == 0)
						return redirect()->route('schools.create');
				}
			return redirect()->route('dashboard');
		} catch (Exception $exc) {
			session()->put('error',
				"Ошибка регистрации нового пользователя: {$exc->getMessage()}");

			return redirect()->route('register');
		}
	}
}
