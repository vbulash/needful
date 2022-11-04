<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Teacher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class Step4bController extends Controller {
	// Выбор
	public function select(Request $request): RedirectResponse {
		$context = session('context');
		$context['teacher'] = Teacher::find($request->teacher);
		$ids = $context['ids'];
		session()->put('context', $context);

		return redirect()->route('e2s.start_internship.step5', ['ids' => json_encode($ids)]);
	}

	//
	public function run(): Factory|View|Application {
		$context = session('context');
		unset($context['teacher']);
		$employer = $context['employer'];

		$teachers = Teacher::whereHasMorph(
			'job',
			[Employer::class],
			function (Builder $builder) use ($employer) {
			    $builder->where('id', $employer->getKey());
		    }
		)->get();

		if ($teachers->count() == 0) {
			event(new ToastEvent('info', '', 'Нет руководителей практики для выбранного работодателя. Необходимо их создать'));
			//return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}

		return view('services.e2s.start_internship.step4b', compact('teachers'));
	}
}
