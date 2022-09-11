<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreLearnRequest;
use App\Models\Learn;
use App\Models\School;
use App\Models\Specialty;
use App\Models\Student;
use App\Notifications\NewLearn;
use App\Notifications\UpdateLearn;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class LearnController extends Controller
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
		$context = session('context');
		$query = Student::findOrFail($context['student'])->learns()->get();

		return Datatables::of($query)
			->editColumn('start', fn($learn) => $learn->start->format('d.m.Y'))
			->editColumn('finish', fn($learn) => $learn->finish ? $learn->finish->format('d.m.Y') : 'н/вр')
			->editColumn('school', fn($learn) => $learn->school ? $learn->school->getTitle() : $learn->new_school)
			->editColumn('specialty', fn($learn) => $learn->specialty ? $learn->specialty->getTitle() : $learn->new_specialty)
			->addColumn('action', function ($learn) {
				$editRoute = route('learns.edit', ['learn' => $learn->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('learns.show', ['learn' => $learn->getKey(), 'sid' => session()->getId()]);
				$actions = '';

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
				$name = $learn->getTitle();
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$learn->getKey()}, '{$name}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index(): View|Factory|Application
	{
		$context = session('context');
		unset($context['learn']);
		session()->put('context', $context);

		$student = Student::findOrFail($context['student']);
		$count = $student->learns()->count();

		return view('learns.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create(): View|Factory|Application
	{
		$mode = config('global.create');
		$schools = School::all()->pluck('short', 'id')->toArray();
		$specialties = Specialty::all()->pluck('name', 'id')->toArray();
		return view('learns.create', compact('mode', 'schools', 'specialties'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreLearnRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreLearnRequest $request): RedirectResponse
	{
		$learn = new Learn();

		$learn->start = $request->start;
		if ($request->has('finish') && $request->finish) $learn->finish = $request->finish;
		$learn->status = $request->status;

		$context = session('context');
		$student = Student::findOrFail($context['student']);
		$learn->student()->associate($student);

		if (isset($request->new_school)) {
			$learn->new_school = $request->new_school;
		} else {
			$school = School::findOrFail($request->school_id);
			$learn->school()->associate($school);
		}

		if (isset($request->new_specialty)) {
			$learn->new_specialty = $request->new_specialty;
		} else {
			$specialty = Specialty::findOrFail($request->specialty_id);
			$learn->specialty()->associate($specialty);
		}

		$learn->save();

		$learn->student->user->notify(new NewLearn($learn));

		session()->put('success', "Запись истории обучения создана");
		return redirect()->route('learns.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(int $id): View|Factory|Application
	{
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false): View|Factory|Application
	{
		$mode = $show ? config('global.show') : config('global.edit');

		$learn = Learn::findOrFail($id);
		$schools = School::all()->pluck('short', 'id')->toArray();
		$specialties = Specialty::all()->pluck('name', 'id')->toArray();
		return view('learns.edit', compact('mode', 'learn', 'schools', 'specialties'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreLearnRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(StoreLearnRequest $request, int $id): RedirectResponse
	{
		$learn = Learn::findOrFail($id);

		$learn->start = $request->start;
		if ($request->has('finish') && $request->finish) $learn->finish = $request->finish;
		else $learn->finish = null;
		$learn->status = $request->status;

		if ($request->status == \App\Models\ActiveStatus::ACTIVE->value) {
			unset($learn->new_school);
			unset($learn->new_specialty);
		} else {
			$learn->new_school = $request->new_school;
			$learn->new_specialty = $request->new_specialty;
		}
		if (isset($request->school_id)) {
			$school = School::findOrFail($request->school_id);
			$learn->school()->associate($school);
		}
		if (isset($request->specialty_id)) {
			$specialty = Specialty::findOrFail($request->specialty_id);
			$learn->specialty()->associate($specialty);
		}

		$learn->update();

		$learn->student->user->notify(new UpdateLearn($learn));

		session()->put('success', "Запись истории обучения обновлена");
		return redirect()->route('learns.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $learn
	 * @return bool
	 */
	public function destroy(Request $request, int $learn): bool
	{
		if ($learn == 0) {
			$id = $request->id;
		} else $id = $learn;

		$learn = Learn::findOrFail($id);
		$name = $learn->getTitle();
		$learn->delete();

		event(new ToastEvent('success', '', "Запись истории обучения '{$name}' удалена"));
		return true;
	}
}
