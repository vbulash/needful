<?php

namespace App\Console;

use App\Models\History;
use App\Models\HistoryStatus;
use App\Notifications\e2s\CancelWarningNotification;
use App\Notifications\e2s\EmployerPracticeStartedNotification;
use App\Notifications\e2s\LastWarningNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use DateTime;

class ProcessTrainings {
	public function __invoke() {
		$histories = History::whereIn('status', [HistoryStatus::NEW ->value, HistoryStatus::PLANNED->value])->get();
		if ($histories->count() > 0) {
			$early = Redis::get('settings.early');
			if (isset($early))
				$early = json_decode($early);
			else {
				$early = (object) [
					'cancel' => 7,
					'last' => 1
				];
				Redis::set('settings.early', json_encode($early));
			}

			$today = (new DateTime())->format('d.m.Y');
			foreach ($histories as $history) {
				$user = $history->timetable->internship->employer->user;
				$start = $history->timetable->start;
				$temp = clone $start;
				$cancelWarning = $temp->modify(sprintf("-%d days", $early->cancel))->format('d.m.Y');
				if ($today == $cancelWarning)
					$user->notify(new CancelWarningNotification($history));

				$temp = clone $start;
				$lastWarning = $temp->modify(sprintf("-%d days", $early->last))->format('d.m.Y');
				if ($today == $lastWarning)
					$user->notify(new LastWarningNotification($history));

				$temp = $start->format('d.m.Y');
				if ($temp == $today)
					$user->notify(new EmployerPracticeStartedNotification($history));
			}
		} else {
			Log::info(sprintf("Контроль стажировок: нет стажировок в статусе \"%s\" или \"%s\"",
				HistoryStatus::getName(HistoryStatus::NEW ->value), HistoryStatus::getName(HistoryStatus::PLANNED->value)));
		}
	}
}
