<?php

namespace App\Http\Controllers\Auth;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Role;
use \Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();
			session()->put('success', "Вы успешно авторизовались");

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
        } catch(Exception $exc) {
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
    public function destroy(Request $request)
    {
        auth()->guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
