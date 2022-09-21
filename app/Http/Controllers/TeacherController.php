<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreTeacherRequest;
use App\Models\Employer;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Notifications\NewLearn;
use App\Notifications\NewTeacher;
use App\Notifications\UpdateTeacher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Exception;

class TeacherController extends Controller
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
		$query = Teacher::all();

		return Datatables::of($query)
			->editColumn('name', fn($teacher) => $teacher->getTitle())
			->addColumn('worksin', fn($teacher) => $teacher->job->short)
			->editColumn('position', fn($teacher) => $teacher->position)
			->addColumn('action', function ($teacher) {
				$editRoute = route('teachers.edit', ['teacher' => $teacher->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('teachers.show', ['teacher' => $teacher->getKey(), 'sid' => session()->getId()]);
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

				$name = $teacher->getTitle();
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$teacher->getKey()}, '{$name}')\">\n" .
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
    public function index()
    {
		$count = Teacher::all()->count();

		return view('teachers.index', compact('count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
	 */
    public function create()
    {
		$mode = config('global.create');
		$schools = School::all()->pluck('short', 'id')->toArray();
		$employers = Employer::all()->pluck('short', 'id')->toArray();

		return view('teachers.create', compact('mode', 'schools', 'employers'));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreTeacherRequest $request
	 * @return RedirectResponse
	 */
    public function store(StoreTeacherRequest $request): RedirectResponse
	{
		$teacher = new Teacher();
		$teacher->name = $request->name;
		$teacher->position = $request->position;
		if ($request->has('in_school')) {	// Руководитель практики работает в учебном заведении
			$school = School::findOrFail($request->school);
			$teacher->job()->associate($school);
		} else {	// Руководитель практики работает у работодателя
			$employer = Employer::findOrFail($request->employer);
			$teacher->job()->associate($employer);
		}
		$teacher->save();

		// О создании руководителя практики уведомить пользователя-владельца учебного заведения или работодателя
		$teacher->job->user->notify(new NewTeacher($teacher));

		session()->put('success', "Руководитель практики &laquo;{$teacher->name}&raquo; создан");
		return redirect()->route('teachers.index', ['sid' => session()->getId()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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
    public function edit(int $id, bool $show = false): Application|Factory|View
	{
		$mode = $show ? config('global.show') : config('global.edit');
		$teacher = Teacher::findOrFail($id);
		$schools = School::all()->pluck('short', 'id')->toArray();
		$employers = Employer::all()->pluck('short', 'id')->toArray();

		return view('teachers.edit', compact('mode', 'teacher', 'schools', 'employers'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreTeacherRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
    public function update(StoreTeacherRequest $request, int $id): RedirectResponse
	{
		$teacher = Teacher::findOrFail($id);
		$teacher->name = $request->name;
		$teacher->position = $request->position;
		if ($request->has('in_school')) {	// Руководитель практики работает в учебном заведении
			$school = School::findOrFail($request->school);
			$teacher->job()->associate($school);
		} else {	// Руководитель практики работает у работодателя
			$employer = Employer::findOrFail($request->employer);
			$teacher->job()->associate($employer);
		}
		$teacher->update();

		// О создании руководителя практики уведомить пользователя-владельца учебного заведения или работодателя
		$teacher->job->user->notify(new UpdateTeacher($teacher));

		session()->put('success', "Руководитель практики &laquo;{$teacher->name}&raquo; изменён");
		return redirect()->route('teachers.index', ['sid' => session()->getId()]);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $teacher
	 * @return bool
	 */
	public function destroy(Request $request, int $teacher)
	{
		if ($teacher == 0) {
			$id = $request->id;
		} else $id = $teacher;

		$teacher = Teacher::findOrFail($id);
		$name = $teacher->getTitle();
		$teacher->delete();

		event(new ToastEvent('success', '', "Руководитель практики &laquo;{$name}&raquo; удалён"));
		return true;
	}
}
