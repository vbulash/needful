<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;

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
			->editColumn('fio', fn($learn) => $teacher->student->getTitle())
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

				$name = $teacher->getTitle();
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
}
