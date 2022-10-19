<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Events\UnreadCountEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\UpdateHistoryRequest;
use App\Models\Employer;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\Right;
use App\Models\Student;
use App\Models\Task;
use App\Models\Timetable;
use App\Models\TraineeStatus;
use App\Models\User;
use App\Notifications\e2s\StartInternshipNotification;
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

		return Datatables::of($query)
			->editColumn('employer', fn ($history) => $history->timetable->internship->employer->getTitle())
			->editColumn('internship', fn ($history) => $history->timetable->internship->getTitle())
			->editColumn('timetable', fn ($history) => $history->timetable->getTitle())
			->editColumn('trainees', fn ($history) =>
				$this->traineeAllLetter($history->students()->wherePivot('status', TraineeStatus::APPROVED->value)->count()) . ' из ' . $history->timetable->planned)
			->editColumn('status', fn ($history) => HistoryStatus::getName($history->status))
			->addColumn('action', function ($history) {
				$editRoute = route('history.edit', ['history' => $history->getKey()]);
				$showRoute = route('history.show', ['history' => $history->getKey()]);
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
		return view('histories.index', $params);
    }

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Factory|View|Application|RedirectResponse
	 */
	public function show(int $id): Factory|View|Application|RedirectResponse
	{
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false): View|Factory|RedirectResponse|Application
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
    public function update(UpdateHistoryRequest $request, int $id): RedirectResponse
	{
		$history = History::findOrFail($id);
		$history->update($request->all());
		//$history->notify(new StartInternshipNotification($history));

		if (!auth()->user()->hasRole(RoleName::ADMIN->value))
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
	public function destroy(Request $request, int $history): bool
	{
		if ($history == 0) {
			$id = $request->id;
		} else $id = $history;

		$history = History::findOrFail($id);
		auth()->user()->disallow($history);
		$history->delete();

		event(new ToastEvent('success', '', "Запись истории стажировок № {$id} удалена"));
		return true;
	}
}
