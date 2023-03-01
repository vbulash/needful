<?php

namespace App\Http\Controllers\Auth;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use \Exception;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller {
	/**
	 * Display the login view.
	 *
	 * @return View
	 */
	public function create() {
		return view('auth.login');
	}

	/**
	 * Handle an incoming authentication request.
	 *
	 * @param LoginRequest $request
	 * @return RedirectResponse
	 */
	public function store(LoginRequest $request) {
		try {
			$request->authenticate();
			$request->session()->regenerate();
			// session()->put('success', "Вы успешно авторизовались");

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
			session()->put('error', $exc->getMessage());
			event(new ToastEvent('error', '', $exc->getMessage()));

			return redirect()->route('login');
		}
	}

	/**
	 * Destroy an authenticated session.
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function destroy(Request $request) {
		auth()->guard('web')->logout();

		$request->session()->invalidate();

		$request->session()->regenerateToken();

		return redirect()->route('login');
	}
}