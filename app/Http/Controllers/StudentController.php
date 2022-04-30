<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use App\Models\User;
use App\Support\PermissionUtils;
use DateTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use \Exception;

class StudentController extends Controller
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
		$query = Student::all();
		if($request->has('ids'))
			$query = $query->whereIn('id', $request->ids);

		return Datatables::of($query)
			->editColumn('fio', fn ($student) => $student->getTitle())
			->editColumn('birthdate', fn ($student) => $student->birthdate->format('d.m.Y'))
			->editColumn('link', fn ($student) => $student->user->name)
			->addColumn('action', function ($student) {
				$editRoute = route('students.edit', ['student' => $student->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('students.show', ['student' => $student->getKey(), 'sid' => session()->getId()]);
				$actions = '';

				if (auth()->user()->can('students.edit'))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				if (auth()->user()->can('students.show'))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				if (auth()->user()->can('students.destroy')) {
					$name = $student->getTitle();
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$student->getKey()}, '{$name}')\">\n" .
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
	public function index()
	{
		$count = Student::all()->count();
		if(auth()->user()->can('students.list')) {
			return view('students.index', compact('count'));
		} elseif (PermissionUtils::can('students.list.')) {
			$ids = PermissionUtils::getPermissionIDs('students.list.');
			return view('students.index', compact('count', 'ids'));
		} elseif (auth()->user()->can('students.create')) {
			return redirect()->route('students.create', ['sid' => session()->getId()]);
		} else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи практиканта'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function create()
	{
		$show = false;
		$baseRight = "students.create";

		if (auth()->user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()
				->map(function ($user) {
					$collect =
						(auth()->user()->getKey() == $user->getKey()) ||
						($user->hasRole('Практикант'))
					;
					if (!$collect) return null;

					return [
						'id' => $user->getKey(),
						'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
					];
				})
				->reject(fn ($value) => $value === null)
				->toArray();
			return view('students.create', compact('users', 'show'));
		} elseif (auth()->user()->can($baseRight))
			return view('students.create', compact('show'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи практиканта'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreStudentRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreStudentRequest $request)
	{
		$student = Student::create($request->all());
		$student->save();
		$name = $student->getTitle();

		$permissions = [
			'students.list',
			'students.edit',
			'students.show'
		];
		foreach ($permissions as $permission) {
			$perm = Permission::findOrCreate($permission . '.' . $student->getKey());
			$student->user->givePermissionTo($perm);
		}

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
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false)
	{
		$student = Student::findOrFail($id);
		$baseRight = sprintf("students.%s", $show ? "show" : "edit");
		$right = sprintf("%s.%d", $baseRight, $student->getKey());
		if (auth()->user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()
				->map(function ($user) {
					$collect =
						(auth()->user()->getKey() == $user->getKey()) ||
						($user->hasRole('Практикант'))
					;
					if (!$collect) return null;

					return [
						'id' => $user->getKey(),
						'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
					];
				})
				->reject(fn ($value) => $value === null)
				->toArray();
			return view('students.edit', compact('student', 'users', 'show'));
		} elseif (auth()->user()->can($baseRight) || auth()->user()->can($right))
			return view('students.edit', compact('student', 'show'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для редактирования / просмотра записи практиканта'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreStudentRequest $request
	 * @param int $id
	 * @return RedirectResponse
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
		$name = $student->getTitle();
		$student->update($request->all());

		$permissions = [
			'students.list',
			'students.edit',
			'students.show'
		];
		foreach ($permissions as $permission) {
			$perm = Permission::findOrCreate($permission . '.' . $student->getKey());
			$student->user->givePermissionTo($perm);
		}

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
		$name = $student->getTitle();
		$student->delete();

		event(new ToastEvent('success', '', "Практикант '{$name}' удалён"));
		return true;
	}
}
