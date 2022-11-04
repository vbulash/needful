<?php

namespace App\Http\Controllers;

use App\Events\All2DestroyedTaskEvent;
use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\UpdateHistoryRequest;
use App\Models\Employer;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\Teacher;
use App\Models\TraineeStatus;
use App\Notifications\e2s\All2DestroyedNotification;
use App\Notifications\e2s\EmployerPracticeDestroyedNotification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class HistoryController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse {
		$query = History::all();

		return Datatables::of($query)
			->editColumn('employer', fn($history) => $history->timetable->internship->employer->getTitle())
			->editColumn('internship', fn($history) => $history->timetable->internship->getTitle())
			->editColumn('timetable', fn($history) => $history->timetable->getTitle())
			->editColumn('teacher', fn($history) => $history->teacher ? $history->teacher->getTitle() : '')
			->editColumn('trainees', fn($history) =>
				$history->status == HistoryStatus::DESTROYED->value ?
				'--' :
				$this->traineeAllLetter($history->students()->wherePivot('status', TraineeStatus::APPROVED->value)->count()) . ' из ' . $history->timetable->planned)
			->editColumn('status', fn($history) => HistoryStatus::getName($history->status))
			->addColumn('action', function ($history) {
			    $editRoute = route('history.edit', ['history' => $history->getKey()]);
			    $showRoute = route('history.show', ['history' => $history->getKey()]);
			    $selectRoute = route('history.select', ['history' => $history->getKey()]);
			    $actions = '';

			    if (!auth()->user()->hasRole(RoleName::TRAINEE->value))
				    if ($history->status == HistoryStatus::DESTROYED->value)
					    $actions .= <<<EOB
<button class="btn btn-primary btn-sm float-left me-1"
	data-toggle="tooltip" data-placement="top" title="Редактирование"
	disabled
>
	<i class="fas fa-edit"></i>\n
</button>
EOB;
				    else
					    $actions .= sprintf(<<<EOB
<a href="%s" class="btn btn-primary btn-sm float-left me-1"
	data-toggle="tooltip" data-placement="top" title="Редактирование"
>
	<i class="fas fa-edit"></i>\n
</a>
EOB,
					    	$editRoute);

			    $actions .= sprintf(<<<EOB
<a href="%s" class="btn btn-primary btn-sm float-left me-1"
	data-toggle="tooltip" data-placement="top" title="Просмотр">
	<i class="fas fa-eye"></i>
</a>
EOB,
			    	$showRoute);

			    if (auth()->user()->hasAnyRole([RoleName::ADMIN->value, RoleName::EMPLOYER->value]))
				    if ($history->status == HistoryStatus::DESTROYED->value)
					    $actions .= <<<EOB
<button class="btn btn-primary btn-sm float-left"
	data-toggle="tooltip" data-placement="top" title="Удаление"
	disabled
>
	<i class="fas fa-trash-alt"></i>
</button>
EOB;
				    else
					    $actions .= sprintf(<<<EOB
<a href="javascript:void(0)" class="btn btn-primary btn-sm float-left"
	data-toggle="tooltip" data-placement="top" title="Удаление" onclick="clickDelete(%d)"
>
	<i class="fas fa-trash-alt"></i>
</a>
EOB,
					    	$history->getKey());

			    $actions .= sprintf(<<<EOB
<a href="{$selectRoute}" class="btn btn-primary btn-sm float-left ms-5"
	data-toggle="tooltip" data-placement="top" title="Выбор">
	<i class="fas fa-check"></i>
</a>
EOB,
			    	$selectRoute);
			    return $actions;
		    })
			->make(true);
	}

	public function select(int $history): RedirectResponse {
		session()->put('context', ['history' => $history]);

		return redirect()->route('trainees.index');
	}

	private function traineeAllLetter(int $count): string {
		$letter = $count . ' ';
		if (($count < 10) || ($count > 20)) {
			$letter .= match ($count % 10) {
				1 => 'практикант',
				2, 3, 4 => 'практиканта',
				default => 'практикантов',
			};
		} else
			$letter .= 'практикантов';

		return $letter;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function index(): View|Factory|RedirectResponse|Application {
		session()->forget('context');

		$params = [
			'count' => History::all()->count()
		];
		return view('histories.index', $params);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Factory|View|Application|RedirectResponse
	 */
	public function show(int $id): Factory|View|Application|RedirectResponse {
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false): View|Factory|RedirectResponse|Application {
		$mode = $show ? config('global.show') : config('global.edit');
		$history = History::findOrFail($id);
		$teacher = $history->teacher ? $history->teacher->getTitle() : '';

		return view('histories.edit', compact('history', 'mode', 'teacher'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateHistoryRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateHistoryRequest $request, int $id): RedirectResponse {
		$history = History::findOrFail($id);
		$history->update($request->all());
		//$history->notify(new StartInternshipNotification($history));

		auth()->user()->allow($history);

		session()->put('success', "Запись истории стажировки № " . $history->getKey() . " обновлена");
		return redirect()->route('history.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $history
	 * @return bool
	 */
	public function destroy(Request $request, int $history): bool {
		if ($history == 0) {
			$id = $request->id;
		} else
			$id = $history;

		$history = History::findOrFail($id);
		auth()->user()->disallow($history);
		$history->delete();

		event(new ToastEvent('success', '', "Запись истории стажировок № {$id} удалена"));
		return true;
	}

	public function canDestroy(Request $request): string {
		$history = History::findOrFail($request->history);
		foreach ($history->students as $student)
			if (!in_array($student->pivot->status, [TraineeStatus::NEW ->value]))
				return "false";
		return "true";
	}

	public function cancel(Request $request): void {
		$history = History::findOrFail($request->history);
		$history->update(['status' => HistoryStatus::DESTROYED->value]);
		foreach ($history->students as $student) {
			$name = $student->getTitle();
			switch ($student->pivot->status) {
				case TraineeStatus::NEW ->value:
					$history->students()->detach($student);
					event(new ToastEvent('success', '', "Приглашение практиканта &laquo;$name&raquo; удалено"));
					break;
				case TraineeStatus::CANCELLED->value:
					$history->students()->updateExistingPivot($student, ['status' => TraineeStatus::DESTROYED->value]);
					event(new ToastEvent('success', '', "Приглашение практиканта &laquo;$name&raquo; отменено"));
					break;
				default:
					event(new All2DestroyedTaskEvent($history, $student));
					$student->notify(new All2DestroyedNotification($history, $student));
					$history->students()->updateExistingPivot($student, ['status' => TraineeStatus::DESTROYED->value]);
					event(new ToastEvent('success', '', "Приглашение практиканта &laquo;$name&raquo; отменено"));
					break;
			}
		}
		$history->timetable->internship->employer->user->notify(new EmployerPracticeDestroyedNotification($history));
		event(new ToastEvent('success', '', 'Стажировка № ' . $history->getKey() . ' отменена'));
	}
}
