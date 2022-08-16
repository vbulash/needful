<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Models\Employer;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
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
	public function getData(Request $request)
	{
		$query = Teacher::all();

		return Datatables::of($query)
			->editColumn('name', fn($teacher) => $teacher->getTitle())
			->addColumn('worksin', fn($teacher) => $teacher->job->name)
			->editColumn('position', fn($teacher) => $teacher->position)
			->addColumn('action', function ($teacher) {
				$editRoute = route('teachers.edit', ['teacher' => $teacher->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('teachers.show', ['teacher' => $teacher->getKey(), 'sid' => session()->getId()]);
				$selectRoute = route('teachers.select', ['teacher' => $teacher->id, 'sid' => session()->getId()]);
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

				$actions .=
					"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
					"<i class=\"fas fa-check\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	public function select(int $id)
	{
		$teacher = Teacher::findOrFail($id);
		session()->forget('context');
		session()->put('context', ['teacher' => $teacher->getKey()]);

		// TODO Раскомментировать по готовности tstudents
		//return redirect()->route('tstudents.index', ['sid' => session()->getId()]);
	}

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
	 */
    public function index()
    {
		session()->forget('context');
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
		$employers = Employer::all()->pluck('name', 'id')->toArray();

		return view('teachers.create', compact('mode', 'schools', 'employers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
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

		event(new ToastEvent('success', '', "Наставник '{$name}' удалён"));
		return true;
	}
}
