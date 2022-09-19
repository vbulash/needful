<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Models\Employer;
use App\Models\History;
use App\Models\Student;
use App\Models\Timetable;
use App\Models\User;
use App\Notifications\e2s\StartInternshipNotification;
use App\Support\PermissionUtils;
use Illuminate\Contracts\Foundation\Application;
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
			->editColumn('employer', function ($history) {
				return $history->timetable->internship->employer->getTitle();
			})
			->editColumn('internship', function ($history) {
				return $history->timetable->internship->getTitle();
			})
			->editColumn('timetable', function ($history) {
				return $history->timetable->getTitle();
			})
			->editColumn('student', function ($history) {
				return $history->student->getTitle();
			})
			->addColumn('action', function ($history) {
				$editRoute = route('history.edit', ['history' => $history->id, 'sid' => session()->getId()]);
				$showRoute = route('history.show', ['history' => $history->id, 'sid' => session()->getId()]);
				$actions = '';

				if (!auth()->user()->hasRole('Практикант'))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				if (auth()->user()->hasRole('Администратор')) {
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$history->id}, '')\">\n" .
						"<i class=\"fas fa-trash-alt\"></i>\n" .
						"</a>\n";
				}
				return $actions;
			})
			->make(true);
	}

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
	 */
    public function index(): View|Factory|RedirectResponse|Application
	{
		$params = [
			'count' => History::all()->count()
		];
		if(auth()->user()->hasRole('Работодатель')) {
			if (auth()->user()->can('employers.list')) {
				// Работодатель имеет право на полный список - ничего не делаем
			} elseif (PermissionUtils::can('employers.list.')) {
				$params['eids'] = PermissionUtils::getPermissionIDs('employers.list.');
			}
		} elseif(auth()->user()->hasRole('Практикант')) {
			if (auth()->user()->can('students.list')) {
				// Практикант имеет право на полный список - ничего не делаем
			} elseif (PermissionUtils::can('students.list.')) {
				$params['sids'] = PermissionUtils::getPermissionIDs('students.list.');
			}
		}
		return view('histories.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
	{
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
	{
        //
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
     * @param Request $request
     * @param  int  $id
     * @return RedirectResponse
	 */
    public function update(Request $request, $id): RedirectResponse
	{
		$history = History::findOrFail($id);
		$history->update($request->all());
		$history->notify(new StartInternshipNotification($history));

		session()->put('success', "Запись истории стажировки № " . $history->getKey() .
			" обновлена<br/>Письмо практиканту отправлено");
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
}
