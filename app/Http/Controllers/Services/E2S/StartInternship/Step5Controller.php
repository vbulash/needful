<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\InviteTraineeTaskEvent;
use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\TraineeStatus;
use App\Notifications\e2s\EmployerPracticeNotification;
use App\Notifications\e2s\StartInternshipNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class Step5Controller extends Controller
{
	//
	public function run(Request $request)
	{
		$context = session('context');
		$ids = $request->ids;

		$view = 'services.e2s.start_internship.step5';

		return view($view, compact('context', 'ids'));
	}

	// Создание
	public function create(Request $request): RedirectResponse
	{
		$context = session('context');
		$ids = $request->ids;

		$history = new History();
		$history->timetable()->associate($context['timetable']->getKey());
		$history->status = HistoryStatus::NEW;
		$history->save();
		$history->students()
			->syncWithPivotValues(json_decode($ids), ['status' => TraineeStatus::NEW]);

//		foreach ($history->students as $trainee) {
			//$history->students()->updateExistingPivot($trainee, ['status' => TraineeStatus::ASKED->value]);
			//$trainee->notify(new EmployerPracticeNotification($trainee, $history, TraineeStatus::ASKED->value));
//			event(new ToastEvent('info', '', "Переслано письмо-предложение практики для учащегося: {$trainee->getTitle()}"));
//			event(new InviteTraineeTaskEvent($history, $trainee));
//		}

		$id = $history->getKey();
		//session()->forget('context');

		session()->put('success', "Стажировка № {$id} запланирована<br/>Письма отправлены");
		return redirect()->route('history.show', ['history' => $id, 'sid' => session()->getId()]);
	}
}
