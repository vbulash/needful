<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Events\UnreadCountEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\UpdateHistoryRequest;
use App\Models\Employer;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\Student;
use App\Models\Task;
use App\Models\Timetable;
use App\Models\TraineeStatus;
use App\Models\User;
use App\Notifications\e2s\StartInternshipNotification;
use App\Support\PermissionUtils;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Exception;

class HistoryController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse
	{
		$query = History::all();
		if ($request->has('eids')) {
			$timetables = [];
			foreach (Employer::all()->whereIn('id', $request->eids) as $employer)
				foreach ($employer->internships->timetables->toArray() as $timetable)
					$timetables[] = $timetable;
			$query = $query->whereIn('timetable_id', $timetables);
		}
		if ($request->has('sids')) {
			$students = Student::all()->whereIn('id', $request->sids)->pluck('id')->toArray();
			$query = $query->whereIn('student_id', $students);
		}

		return Datatables::of($query)
			->editColumn('employer', fn ($history) => $history->timetable->internship->employer->getTitle())
			->editColumn('internship', fn ($history) => $history->timetable->internship->getTitle())
			->editColumn('timetable', fn ($history) => $history->timetable->getTitle())
			->editColumn('trainees', fn ($history) =>
				$this->traineeAllLetter($history->students()->wherePivot('status', TraineeStatus::ACCEPTED->value)->count()) . ' из ' . $history->timetable->planned)
			->editColumn('status', fn ($history) => HistoryStatus::getName($history->status))
			->addColumn('action', function ($history) {
				$editRoute = route('history.edit', ['history' => $history->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('history.show', ['history' => $history->getKey(), 'sid' => session()->getId()]);
				$selectRoute = route('history.select', ['history' => $history->getKey()]);
				$actions = '';

				if (!auth()->user()->hasRole(RoleName::TRAINEE->value))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$history->getKey()}, '')\">\n" .
						"<i class=\"fas fa-trash-alt\"></i>\n" .
						"</a>\n";
				}
				$actions .=
					"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
					"<i class=\"fas fa-check\"></i>\n" .
					"</a>\n";
				return $actions;
			})
			->make(true);
	}

	public function select(int $history): RedirectResponse
	{
		session()->put('context', ['history' => $history]);

		return redirect()->route('trainees.index');
	}

	private function traineeAllLetter(int $count): string {
		$letter = $count . ' ';
		if(($count < 10) || ($count > 20)) {
			$letter .= match ($count % 10) {
				1 => 'практикант',
				2, 3, 4 => 'практиканта',
				default => 'практикантов',
			};
		} else $letter .= 'практикантов';

		return $letter;
	}

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
	 */
    public function index(): View|Factory|RedirectResponse|Application
	{
		session()->forget('context');

		$params = [
			'count' => History::all()->count()
		];
		if(auth()->user()->hasRole(RoleName::EMPLOYER->value)) {
			if (auth()->user()->can('employers.list')) {
				// Работодатель имеет право на полный список - ничего не делаем
			} elseif (PermissionUtils::can('employers.list.')) {
				$params['eids'] = PermissionUtils::getPermissionIDs('employers.list.');
			}
		} elseif(auth()->user()->hasRole(RoleName::TRAINEE->value)) {
			if (auth()->user()->can('students.list')) {
				// Практикант имеет право на полный список - ничего не делаем
			} elseif (PermissionUtils::can('students.list.')) {
				$params['sids'] = PermissionUtils::getPermissionIDs('students.list.');
			}
		}
		return view('histories.index', $params);
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Application|Factory|View
	 */
	public function show($id)
	{
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false)
	{
		$mode = $show ? config('global.show') : config('global.edit');
		$history = History::findOrFail($id);
		return view('histories.edit', compact('history', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateHistoryRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
    public function update(UpdateHistoryRequest $request, $id): RedirectResponse
	{
		$history = History::findOrFail($id);
		$history->update($request->all());
		//$history->notify(new StartInternshipNotification($history));

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
	public function destroy(Request $request, int $history): bool
	{
		if ($history == 0) {
			$id = $request->id;
		} else $id = $history;

		$history = History::findOrFail($id);
		$history->delete();

		event(new ToastEvent('success', '', "Запись истории стажировок № {$id} удалена"));
		return true;
	}

	private function changeStatus(Request $request, int $toStatus): Response|Application|ResponseFactory
	{
		$history = History::findOrFail($request->history);
		$trainee = Student::findOrFail($request->trainee);
		$task = $request->has('task') ? $request->task : 0;
		$status = $history->students()->findOrFail($request->trainee)->pivot->status;

		$redirect = null;
		$updated = false;
		switch ($status) {
			case TraineeStatus::ASKED->value:
				$kind = 'success';
				$message = match ($toStatus) {
					TraineeStatus::ACCEPTED->value => 'Вы согласились участвовать в стажировке',
					TraineeStatus::REJECTED->value => 'Вы отказались от участия в стажировке'
				};
				$redirect = route('inbox.archive');
				$history->students()->updateExistingPivot($trainee, ['status' => $toStatus]);
				$updated = (Task::findOrFail($task))->update([
					'read' => true,
					'archive' => true
				]);
				$response = 200;
				break;
			default:
				$kind = 'error';
				$message = sprintf(
					"Нельзя изменить статус записи стажировки практиканта с &laquo;%s&raquo; на &laquo;%s&raquo;",
					TraineeStatus::getName($status), TraineeStatus::getName($toStatus));
				$response = 204;
		}
		event(new ToastEvent($kind, '', $message));
		if ($updated)
			event(new UnreadCountEvent());
		return response(content: $redirect, status: $response);

	}

	public function accept(Request $request): Response|Application|ResponseFactory
	{
		return $this->changeStatus($request, TraineeStatus::ACCEPTED->value);
	}

	public function reject(Request $request): Response|Application|ResponseFactory
	{
		return $this->changeStatus($request, TraineeStatus::REJECTED->value);
	}
}
