<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreInternshipRequest;
use App\Http\Requests\UpdateInternshipRequest;
use App\Models\Employer;
use App\Models\Internship;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use \Exception;

class InternshipController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @param int $employer
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request, int $employer)
	{
		$context = session('context');
		if (isset($context['chain']))
			$query = Internship::where('id', $context['internship']);
		else
			$query = Employer::findOrFail($employer)->internships()->get();

		return Datatables::of($query)
			->editColumn('itype', function ($internship) {
				switch ($internship->itype) {
					case 'Открытая стажировка':
						return 'Открытая';
					case 'Закрытая стажировка':
						return 'Закрытая';
				}
				return '';
			})
			->addColumn('action', function ($internship) use ($context) {
				$editRoute = route('internships.edit', ['internship' => $internship->id, 'sid' => session()->getId()]);
				$showRoute = route('internships.show', ['internship' => $internship->id, 'sid' => session()->getId()]);
				$timetablesRoute = route('internships.timetables', ['internship' => $internship->id, 'sid' => session()->getId()]);
				$especialtiesRoute = route('internships.especialties', ['internship' => $internship->id, 'sid' => session()->getId()]);

				$actions = '';

				if (!isset($context['chain']))
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
				if (!isset($context['chain']))
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$internship->id}, '{$internship->iname}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";

				if (isset($context['chain'])) {
					$actions .=
						"<a href=\"{$timetablesRoute}\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
						"<i class=\"fas fa-check\"></i>\n" .
						"</a>\n";
				} else {
					$actions .=
						"<a href=\"{$especialtiesRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Специальности для стажировки\">\n" .
						"<i class=\"fas fa-people-arrows\"></i>\n" .
						"</a>\n";
					$actions .=
						"<a href=\"{$timetablesRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Графики стажировки\">\n" .
						"<i class=\"fas fa-calendar-check\"></i>\n" .
						"</a>\n";
				}
				return $actions;
			})
			->make(true);
	}

	private function next(string $view, int $id)
	{
		$internship = Internship::findOrFail($id);

		$context = session('context');
		if (!isset($context['chain'])) {
			$context = session('context');
			unset($context['internship']);
			unset($context['timetable']);
			$context['internship'] = $internship->getKey();
			session()->put('context', $context);
		}

		return redirect()->route($view, ['sid' => session()->getId()]);
	}

	public function timetables(int $id)
	{
		return $this->next('timetables.index', $id);
	}

	public function especialties(int $id)
	{
		return $this->next('especialties.index', $id);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function index(Request $request): View|Factory|Application
	{
		$context = session('context');
		$employer = Employer::findOrFail($context['employer']);
		if (!isset($context['chain'])) {
			unset($context['especialty']);
			unset($context['timetable']);
			$count = $employer->internships()->count();
			session()->put('context', $context);
		} else $count = 1;

		return view('internships.index', compact('employer', 'count'));
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
		$employer = Employer::findOrFail($context['employer']);
		return view('internships.create', compact('employer', 'mode'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreInternshipRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreInternshipRequest $request): RedirectResponse
	{
		$internship = Internship::create($request->all());
		$internship->save();
		$name = $internship->iname;

		session()->put('success', "Стажировка \"{$name}\" создана");
		return redirect()->route('internships.index', ['employer' => $internship->employer->getKey(), 'sid' => session()->getId()]);
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
		$internship = Internship::findOrFail($id);
		return view('internships.edit', compact('internship', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateInternshipRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateInternshipRequest $request, $id): RedirectResponse
	{
		$internship = Internship::findOrFail($id);
		$name = $internship->iname;
		$internship->update($request->all());

		session()->put('success', "Стажировка \"{$name}\" обновлена");
		return redirect()->route('internships.index', ['employer' => $internship->employer->getKey(), 'sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $internship
	 * @return bool
	 */
	public function destroy(Request $request, int $internship): bool
	{
		if ($internship == 0) {
			$id = $request->id;
		} else $id = $internship;

		$internship = Internship::findOrFail($id);
		$name = $internship->iname;
		$internship->delete();

		event(new ToastEvent('success', '', "Стажировка '{$name}' удалена"));
		return true;
	}
}
