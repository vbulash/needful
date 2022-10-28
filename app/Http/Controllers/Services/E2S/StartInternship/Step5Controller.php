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
		$ids = $request->ids;
		return view('services.e2s.start_internship.step5', compact('context', 'ids'));
	}

	// Создание
	public function create(Request $request): RedirectResponse {
		$context = session('context');
		$ids = $request->ids;

		$history = new History();
		$history->timetable()->associate($context['timetable']->getKey());
		$history->status = HistoryStatus::NEW;
		$history->save();
		$history->students()
			->syncWithPivotValues(json_decode($ids), ['status' => TraineeStatus::NEW ]);

		$history->timetable->internship->employer->user->notify(new EmployerPracticeCreatedNotification($history));
		event(new ToastEvent('info', '', 'Создана стажировка &laquo;' . $history->getTitle() . '&raquo;'));

		auth()->user()->allow($history);

		$id = $history->getKey();
		//session()->forget('context');

		session()->put('success', "Стажировка № {$id} запланирована");
		return redirect()->route('history.show', ['history' => $id]);
	}
}
