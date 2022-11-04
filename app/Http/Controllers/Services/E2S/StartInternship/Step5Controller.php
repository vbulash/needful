<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\InviteTraineeTaskEvent;
use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\Right;
use App\Models\TraineeStatus;
use App\Notifications\e2s\EmployerPracticeCreatedNotification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class Step5Controller extends Controller {
	//
	public function run(Request $request): Factory|View|Application {
		$context = session('context');
		$ids = json_encode($context['ids']);
		return view('services.e2s.start_internship.step5', compact('context', 'ids'));
	}

	// Создание
	public function create(Request $request): RedirectResponse {
		$context = session('context');
		$ids = $context['ids'];

		$history = new History();
		$history->timetable()->associate($context['timetable']->getKey());
		$history->status = HistoryStatus::NEW;
		$history->teacher()->associate($context['teacher']->getKey());
		$history->save();
		$history->students()
			->syncWithPivotValues($ids, ['status' => TraineeStatus::NEW ]);

		$employer = $history->timetable->internship->employer;
		$employer->user->notify(new EmployerPracticeCreatedNotification($history));
		event(new ToastEvent('info', '', 'Создана стажировка &laquo;' . $history->getTitle() . '&raquo;'));

		$employer->user->allow($history);

		$id = $history->getKey();
		//session()->forget('context');

		session()->put('success', "Стажировка № {$id} создана");
		return redirect()->route('history.show', ['history' => $id]);
	}
}
