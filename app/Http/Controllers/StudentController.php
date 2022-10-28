<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Events\UpdateStudentTaskEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\ActiveStatus;
use App\Models\Right;
use App\Models\Student;
use App\Models\TraineeStatus;
use App\Models\User;
use App\Notifications\NewStudent;
use App\Notifications\UpdateStudent;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class StudentController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request) {
		$query = Student::all();

		return Datatables::of($query)
			->editColumn('fio', fn($student) => $student->getTitle())
			->editColumn('birthdate', fn($student) => $student->birthdate->format('d.m.Y'))
			->editColumn('link', fn($student) => $student->user->name)
			->addColumn('action', function ($student) {
			    $editRoute = route('students.edit', ['student' => $student->getKey(), 'sid' => session()->getId()]);
			    $showRoute = route('students.show', ['student' => $student->getKey(), 'sid' => session()->getId()]);
			    $selectRoute = route('students.select', ['student' => $student->id, 'sid' => session()->getId()]);
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

			    if ($student->status == ActiveStatus::ACTIVE->value)
				    $actions .=
				    	"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
				    	"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
				    	"<i class=\"fas fa-check\"></i>\n" .
				    	"</a>\n";

			    return $actions;
		    })
			->make(true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function index(): View|Factory|RedirectResponse|Application {
		session()->forget('context');
		$count = Student::all()->count();
		return view('students.index', compact('count'));
	}

	public function select(int $id): RedirectResponse {
		$student = Student::findOrFail($id);
		session()->forget('context');
		session()->put('context', ['student' => $student->getKey()]);

		return redirect()->route('learns.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function create(Request $request): View|Factory|RedirectResponse|Application {
		$mode = config('global.create');
		$baseRight = "students.create";

		$params = [
			'mode' => $mode
		];
		if (isset($request->for))
			$params['for'] = $request->for;
		if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
			$temp = User::orderBy('name')->get()
				->map(function ($user) {
				    $collect =
				    	(auth()->user()->getKey() == $user->getKey()) ||
				    	($user->hasRole(RoleName::TRAINEE->value));
				    if (!$collect)
					    return null;

				    return [
				    	'id' => $user->getKey(),
				    	'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
				    ];
			    })
				->reject(fn($value) => $value === null);
			if (env('SIMPLIFIED_USER_CREATION')) {
				$special = User::special();
				$users = collect([$special->getKey() => $special->name])
					->map(fn($item, $key) => ['id' => $key, 'name' => $item])
					->merge($temp)
					->toArray();
			} else
				$users = $temp;
			$params['users'] = $users;
			return view('students.create', $params);
		} elseif (auth()->user()->can($baseRight))
			return view('students.create', $params);
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи учащегося'));
			return redirect()->route('dashboard');
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreStudentRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreStudentRequest $request): RedirectResponse {
		$special = User::special()->getKey() == $request->user_id;
		$student = Student::create($request->all());
		$student->save();
		$name = $student->getTitle();

		auth()->user()->allow($student);

		$student->user->notify(new NewStudent($student));

		session()->put('success', "Учащийся \"{$name}\" создан");
		return $special ? redirect()->route('users.create', ['for' => $student->getKey()]) : redirect()->route('students.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(int $id) {
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');

		$student = Student::findOrFail($id);
		$baseRight = sprintf("students.%s", $show ? "show" : "edit");

		if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
			$special = User::special();
			$temp = User::orderBy('name')->get()
				->map(function ($user) {
				    $collect =
				    	(auth()->user()->getKey() == $user->getKey()) ||
				    	($user->hasRole(RoleName::TRAINEE->value));
				    if (!$collect)
					    return null;

				    return [
				    	'id' => $user->getKey(),
				    	'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
				    ];
			    })
				->reject(fn($value) => $value === null);

			if ($special->getKey() == $student->user->getKey())
				$users = collect([$special->getKey() => $special->name])
					->map(fn($item, $key) => ['id' => $key, 'name' => $item])
					->merge($temp)
					->toArray();
			else
				$users = $temp->toArray();

			return view('students.edit',
				compact('student', 'users', 'mode'));
		} elseif (auth()->user()->can($baseRight) || auth()->user()->allowed($student))
			return view('students.edit',
				compact('student', 'mode'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для редактирования / просмотра записи учащегося'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateStudentRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateStudentRequest $request, int $id) {
		$student = Student::findOrFail($id);
		$oldStatus = $student->status;
		$data = $request->all();
		$student->update($data);
		$name = $student->getTitle();
		$newStatus = $student->status;

		auth()->user()->allow($student);

		$student->user->notify(new UpdateStudent($student));
		if ($oldStatus != $newStatus && $newStatus == ActiveStatus::ACTIVE->value)
			event(new UpdateStudentTaskEvent($student));

		session()->put('success', "Анкета учащегося \"{$name}\" обновлена");
		return redirect()->route('students.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $student
	 * @return bool
	 */
	public function destroy(Request $request, int $student) {
		if ($student == 0) {
			$id = $request->id;
		} else
			$id = $student;

		$student = Student::findOrFail($id);
		$name = $student->getTitle();

		auth()->user()->disallow($student);

		$student->delete();

		event(new ToastEvent('success', '', "Учащийся '{$name}' удалён"));
		return true;
	}
}
