<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreTStudentRequest;
use App\Models\Learn;
use App\Models\School;
use App\Models\Specialty;
use App\Models\Student;
use App\Models\Teacher;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Yajra\DataTables\DataTables;

class TeacherStudentController extends Controller
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
		$teacher = Teacher::findOrFail($context['teacher']);
		// Для руководителя практики от учебного заведения = +where('school_id', $teacher->school->getKey())
		$query = $teacher->learns->whereNull('finish');

		return Datatables::of($query)
			->editColumn('fio', fn($learn) => $learn->student->getTitle())
			->editColumn('school', fn($learn) => $learn->school->getTitle())
			->editColumn('specialty', fn($learn) => $learn->specialty->getTitle())
			->addColumn('action', function ($learn) use ($teacher) {
				$editRoute = route('tstudents.edit', ['tstudent' => $learn->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('tstudents.show', ['tstudent' => $learn->getKey(), 'sid' => session()->getId()]);

				$actions =
					"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-edit\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";

				$name = $learn->student->getTitle();
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
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
		$teacher = Teacher::findOrFail($context['teacher']);
		// Для руководителя практики от учебного заведения = +where('school_id', $teacher->school->getKey())
		$count = $teacher->learns->whereNull('finish')->count();
		return view('tstudents.index', compact('teacher', 'count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create(): View|Factory|Application
	{
		$mode = config('global.create');
		$context = session('context');
		$teacher = Teacher::findOrFail($context['teacher']);
		$specialties = Specialty::all()->pluck('name', 'id')->toArray();
		return view('tstudents.create', compact('mode', 'teacher', 'specialties'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreTStudentRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreTStudentRequest $request): RedirectResponse
	{
		$context = session('context');
		$teacher = Teacher::findOrFail($context['teacher']);
		$teacher->learns()->attach($request->student);

		session()->put('success', "Привязка практиканта к руководителю практики выполнена");
		return redirect()->route('tstudents.index', ['sid' => session()->getId()]);
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

		$context = session('context');
		$teacher = Teacher::findOrFail($context['teacher']);
		$learn = Learn::findOrFail($id);
		$specialties = Specialty::all()->pluck('name', 'id')->toArray();
		$learns = $this->getLearns($learn->specialty->getKey(),
			$teacher->job->getMorphClass() == School::class ? $teacher->job->getKey() : 0)
			->map(fn($value, $key) => Student::findOrFail($value)->getTitle())
			->toArray();
		return view('tstudents.edit', compact('mode', 'teacher', 'learn', 'learns', 'specialties'));
	}

	/**
	 * @param StoreTStudentRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(StoreTStudentRequest $request, int $id): RedirectResponse
	{
		$context = session('context');
		$teacher = Teacher::findOrFail($context['teacher']);
		$learn = Learn::findOrFail($id);
		if ($request->student != $learn->getKey()) {
			$teacher->learns()->detach($learn->getKey());
			$teacher->learns()->attach($request->student);
		}

		session()->put('success', "Обновление привязки практиканта к руководителю практики выполнено");
		return redirect()->route('tstudents.index', ['sid' => session()->getId()]);
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

		$context = session('context');
		$teacher = Teacher::findOrFail($context['teacher']);
		$teacher->learns()->detach($id);
		$learn = Learn::findOrFail($id);
		$name = $learn->student->getTitle();

		event(new ToastEvent('success', '', "Привязка практиканта '{$name}' к руководителю практики удалена"));
		return true;
	}

	/**
	 * @param Request $request
	 * @return Response|JsonResponse|Application|ResponseFactory
	 */
	public function getSource(Request $request): Response|JsonResponse|Application|ResponseFactory
	{
		$specialty = $request->specialty;
		$school = $request->has('school') ? $request->school : 0;

		$select = $this->getLearns($specialty, $school);
		if ($select) {
			$select = $select->mapWithKeys(fn($value, $key) => [
				'id' => $key,
				'text' => Student::findOrFail($value)->getTitle()
			]);
			return response(content: json_encode($select), status: 200);
		} else {
			return response(status: 204);
		}
	}

	/**
	 * @param int $specialty
	 * @param int $school
	 * @return Collection|null
	 */
	private function getLearns(int $specialty, int $school): ?Collection
	{
		$query = Learn::where('specialty_id', $specialty)
			->whereNull('finish');
		if ($query->count() == 0) return null;

		if ($school != 0)
			$query = $query->where('school_id', $school);

		if ($query->count() == 0) return null;

		return $query->pluck('student_id', 'id');
	}
}
