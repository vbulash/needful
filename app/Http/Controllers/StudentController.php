<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use App\Models\User;
use DateTime;
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

class StudentController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData()
	{
		return Datatables::of(Student::all())
			->editColumn('fio', function ($student) {
				return sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : ''));
			})
			->editColumn('birthdate', function ($student) {
				switch (env('DB_CONNECTION')) {
					case 'sqlite':
						return $student->birthdate;
					case 'mysql':
					default:
						$birthdate = DateTime::createFromFormat('Y-m-d', $student->birthdate);
						return $birthdate->format('d.m.Y');
				}
			})
			->addColumn('action', function ($student) {
				$editRoute = route('students.edit', ['student' => $student->id, 'sid' => session()->getId()]);
				$showRoute = route('students.show', ['student' => $student->id, 'sid' => session()->getId()]);
				$actions = '';

				if (Auth::user()->can('students.edit'))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('students.show'))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('students.destroy'))
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$student->id}, '{$student->name}')\">\n" .
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
		$count = Student::all()->count();
		return view('students.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create()
	{
		if(Auth::user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()->pluck('name', 'id')->toArray();
			return view('students.create', ['users' => $users]);
		} else return view('students.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreStudentRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreStudentRequest $request)
	{
		switch (env('DB_CONNECTION')) {
			case 'sqlite':
				break;
			case 'mysql':
			default:
			$birthdate = DateTime::createFromFormat('d.m.Y', $request->birthdate);
			$request->birthdate = $birthdate->format('Y-m-d');
			break;
		}

		$student = Student::create($request->all());
		$student->save();
		$name = sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : ''));

		session()->put('success', "Практикант \"{$name}\" создан");
		return redirect()->route('students.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(int $id)
	{
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false)
	{
		$student = Student::findOrFail($id);
		return view('students.edit', compact('student', 'show'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreStudentRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 * @return Response
	 */
	public function update(StoreStudentRequest $request, int $id)
	{
		switch (env('DB_CONNECTION')) {
			case 'sqlite':
				break;
			case 'mysql':
			default:
				$birthdate = DateTime::createFromFormat('d.m.Y', $request->birthdate);
				$request->birthdate = $birthdate->format('Y-m-d');
				break;
		}

		$student = Student::findOrFail($id);
		$name = sprintf("%s %s%s", $student->lastname, $student->firstname, ($student->surname ? ' ' . $student->surname : ''));
		$student->update($request->all());

		session()->put('success', "Анкета практиканта \"{$name}\" обновлена");
		return redirect()->route('students.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $student
	 * @return bool
	 */
	public function destroy(Request $request, int $student)
	{
		if ($student == 0) {
			$id = $request->id;
		} else $id = $student;

		$student = Student::findOrFail($id);
		$name = $student->name;
		$student->delete();

		event(new ToastEvent('success', '', "Практикант '{$name}' удалён"));
		return true;
	}
}
