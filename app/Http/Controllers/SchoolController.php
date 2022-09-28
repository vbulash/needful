<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Events\UpdateSchoolTaskEvent;
use App\Http\Requests\StoreSchoolRequest;
use App\Models\ActiveStatus;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use App\Notifications\NewSchool;
use App\Notifications\NewUser;
use App\Notifications\UpdateSchool;
use App\Support\PermissionUtils;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use Exception;

class SchoolController extends Controller
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
		$query = School::all();
		if ($request->has('ids'))
			$query = $query->whereIn('id', $request->ids);

		return Datatables::of($query)
			->addColumn('type', fn ($school) => SchoolType::getName($school->type))
			->editColumn('link', fn ($school) => $school->user->name)
			->addColumn('action', function ($school) {
				$editRoute = route('schools.edit', ['school' => $school->id, 'sid' => session()->getId()]);
				$showRoute = route('schools.show', ['school' => $school->id, 'sid' => session()->getId()]);
				$selectRoute = route('schools.select', ['school' => $school->id, 'sid' => session()->getId()]);
				$actions = '';

				if (auth()->user()->can('schools.edit') || auth()->user()->can('schools.edit.' . $school->getKey()))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left ms-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				if (auth()->user()->can('schools.show') || auth()->user()->can('schools.show.' . $school->getKey()))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left ms-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				if (auth()->user()->can('schools.destroy') || auth()->user()->can('schools.destroy.' . $school->getKey())) {
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$school->id}, '{$school->short}')\">\n" .
						"<i class=\"fas fa-trash-alt\"></i>\n" .
						"</a>\n";
				}

				if ($school->status == ActiveStatus::ACTIVE->value)
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
		session()->forget('context');
		session()->put('context', ['school' => $id]);

		return redirect()->route('fspecialties.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function index()
	{
		session()->forget('context');

		$count = School::all()->count();
		if (auth()->user()->can('schools.list')) {
			return view('schools.index', compact('count'));
		} elseif (PermissionUtils::can('schools.list.')) {
			$ids = PermissionUtils::getPermissionIDs('schools.list.');
			return view('schools.index', compact('count', 'ids'));
		} elseif (auth()->user()->can('schools.create')) {
			return redirect()->route('schools.create', ['sid' => session()->getId()]);
		} else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи учебного заведения'));
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
		$mode = config('global.create');
		$baseRight = "schools.create";
		if (auth()->user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()
				->map(function ($user) {
					$collect =
						(auth()->user()->getKey() == $user->getKey()) ||
						($user->hasRole('Учебное заведение'))
					;
					if (!$collect) return null;

					return [
						'id' => $user->getKey(),
						'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
					];
				})
				->reject(fn ($value) => $value === null)
				->toArray();
			return view('schools.create', compact('users', 'mode'));
		} elseif (auth()->user()->can($baseRight))
			return view('schools.create', compact('mode'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи учебного заведения'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreSchoolRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreSchoolRequest $request)
	{
		$school = School::create($request->all());
		$school->save();
		$name = $school->name;

		$permissions = [
			'schools.list',
			'schools.edit',
			'schools.show'
		];
		foreach ($permissions as $permission) {
			$perm = Permission::findOrCreate($permission . '.' . $school->getKey());
			$school->user->givePermissionTo($perm);
		}

		$school->user->notify(new NewSchool($school));

		session()->put('success', "Учебное заведение \"{$name}\" создано");
		return redirect()->route('schools.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show($id)
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
		session()->forget('context');
		session()->put('context', ['school' => $id]);

		$mode = $show ? config('global.show') : config('global.edit');
		$school = School::findOrFail($id);
		$baseRight = sprintf("schools.%s", $show ? "show" : "edit");
		$right = sprintf("%s.%d", $baseRight, $school->getKey());

		if (auth()->user()->hasRole('Администратор')) {
			$users = User::orderBy('name')->get()
				->map(function ($user) {
					$collect =
						(auth()->user()->getKey() == $user->getKey()) ||
						($user->hasRole('Учебное заведение'))
					;
					if (!$collect) return null;

					return [
						'id' => $user->getKey(),
						'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
					];
				})
				->reject(fn ($value) => $value === null)
				->toArray();
			return view('schools.edit', compact('school', 'users', 'mode'));
		} elseif (auth()->user()->can($baseRight) || auth()->user()->can($right))
			return view('schools.edit', compact('school', 'mode'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для редактирования / просмотра записи учебного заведения'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreSchoolRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(StoreSchoolRequest $request, $id)
	{
		$school = School::findOrFail($id);
		$oldStatus = $school->status;
		$name = $school->name;
		$school->update($request->all());
		$newStatus = $school->status;

		$permissions = [
			'schools.list',
			'schools.edit',
			'schools.show'
		];
		foreach ($permissions as $permission) {
			$perm = Permission::findOrCreate($permission . '.' . $school->getKey());
			$school->user->givePermissionTo($perm);
		}

		$school->user->notify(new UpdateSchool($school));
		if ($oldStatus != $newStatus && $newStatus == ActiveStatus::ACTIVE->value)
			event(new UpdateSchoolTaskEvent($school));

		session()->put('success', "Анкета учебного заведения \"{$name}\" обновлена");
		return redirect()->route('schools.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $school
	 * @return bool
	 */
	public function destroy(Request $request, int $school)
	{
		if ($school == 0) {
			$id = $request->id;
		} else $id = $school;

		$school = School::findOrFail($id);
		$name = $school->name;
		$school->delete();

		event(new ToastEvent('success', '', "Учебное заведение '{$name}' удалено"));
		return true;
	}
}
