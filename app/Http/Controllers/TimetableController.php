<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreTimetableRequest;
use App\Http\Requests\UpdateTimetableRequest;
use App\Models\Employer;
use App\Models\Internship;
use App\Models\Timetable;
use DateTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use \Exception;

class TimetableController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @param int $internship
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request, int $internship)
	{
		$context = session('context');
		if (isset($context['chain']))
			$query = Timetable::where('id', $context['timetable']);
		else
			$query = Internship::findOrFail($internship)->timetables()->get();

		return Datatables::of($query)
			->editColumn('start', fn($timetable) => $timetable->start->format('d.m.Y'))
			->editColumn('end', fn($timetable) => $timetable->end->format('d.m.Y'))
			->addColumn('action', function ($timetable) use ($context) {
				$editRoute = route('timetables.edit', ['timetable' => $timetable->id, 'sid' => session()->getId()]);
				$showRoute = route('timetables.show', ['timetable' => $timetable->id, 'sid' => session()->getId()]);
				$actions = '';

				if (isset($context['chain']))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				else {
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
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$timetable->id}, '')\">\n" .
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
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function index(Request $request)
	{
		$context = session('context');
		$internship = Internship::findOrFail($context['internship']);
		if (isset($context['chain']))
			$count = 1;
		else {
			unset($context['timetable']);
			session()->put('context', $context);
			$count = $internship->timetables()->count();
		}
		return view('timetables.index', compact('internship', 'count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function create(Request $request): View|Factory|Application
	{
		$mode = config('global.create');
		$context = session('context');
		$internship = Internship::findOrFail($context['internship']);
		return view('timetables.create', compact('internship', 'mode'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreTimetableRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreTimetableRequest $request): RedirectResponse
	{
		$timetable = Timetable::create($request->all());
		$timetable->save();
		$name = $timetable->name;

		session()->put('success', "Запись графика практик " . ($name ? "\"{$name}\" " : "") . "создана");
		return redirect()->route('timetables.index', ['internship' => $timetable->internship->getKey()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(Request $request, int $id): View|Factory|Application
	{
		return $this->edit($request, $id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(Request $request, int $id, bool $show = false): View|Factory|Application
	{
		$mode = $show ? config('global.show') : config('global.edit');
		$timetable = Timetable::findOrFail($id);
		return view('timetables.edit', compact('timetable', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateTimetableRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateTimetableRequest $request, $id): RedirectResponse
	{
		$timetable = Timetable::findOrFail($id);
		$name = $timetable->name;
		$timetable->update($request->all());

		session()->put('success', "Запись графика практики " . ($name ? "\"{$name}\" " : "") . "обновлена");
		return redirect()->route('timetables.index', ['internship' => $timetable->internship->getKey()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $timetable
	 * @return bool
	 */
	public function destroy(Request $request, int $timetable)
	{
		if ($timetable == 0) {
			$id = $request->id;
		} else $id = $timetable;

		$timetable = Timetable::findOrFail($id);
		$name = $timetable->iname;
		$timetable->delete();

		event(new ToastEvent('success', '', "Запись графика практики " . ($name ? "\"{$name}\" " : "") . "удалена"));
		return true;
	}
}
