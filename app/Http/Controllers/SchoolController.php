<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Events\UpdateSchoolTaskEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\StoreSchoolRequest;
use App\Models\ActiveStatus;
use App\Models\Right;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use App\Notifications\NewSchool;
use App\Notifications\UpdateSchool;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use Exception;

class SchoolController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse {
		$query = School::all();

		return Datatables::of($query)
			->addColumn('type', fn($school) => SchoolType::getName($school->type))
			->editColumn('link', fn($school) => $school->user->name)
			->addColumn('action', function ($school) {
				$editRoute = route('schools.edit', ['school' => $school->id]);
				$showRoute = route('schools.show', ['school' => $school->id]);
				$selectRoute = route('schools.select', ['school' => $school->id]);
				$items = [];

				if (auth()->user()->can('schools.edit') || auth()->user()->allowed($school))
					$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];
				if (auth()->user()->can('schools.show') || auth()->user()->allowed($school))
					$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				if (auth()->user()->can('schools.destroy') || auth()->user()->allowed($school))
					$items[] = ['type' => 'item', 'click' => "clickDelete({$school->id}, '{$school->short}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];

				if ($school->status == ActiveStatus::ACTIVE->value) {
					$items[] = ['type' => 'divider'];
					$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Специальности'];
					$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Заявки на практику'];
				}
				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function select(int $id) {
		session()->forget('context');
		session()->put('context', ['school' => $id]);

		return redirect()->route('fspecialties.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index(): View|Factory|Application {
		session()->forget('context');

		$count = School::all()->count();
		return view('schools.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function create() {
		$mode = config('global.create');
		$baseRight = "schools.create";
		if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
			$users = User::orderBy('name')->get()
				->map(function ($user) {
					$collect =
						(auth()->user()->getKey() == $user->getKey()) ||
						($user->hasRole(RoleName::SCHOOL->value))
					;
					if (!$collect)
						return null;

					return [
						'id' => $user->getKey(),
						'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
					];
				})
				->reject(fn($value) => $value === null)
				->toArray();
			return view('schools.create', compact('users', 'mode'));
		} elseif (auth()->user()->can($baseRight))
			return view('schools.create', compact('mode'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи образовательного учреждения'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreSchoolRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreSchoolRequest $request) {
		$school = School::create($request->all());
		$school->save();
		$name = $school->name;

		auth()->user()->allow($school);

		$school->user->notify(new NewSchool($school));

		session()->put('success', "Образовательное учреждение \"{$name}\" создано");
		return redirect()->route('schools.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show($id) {
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false) {
		session()->forget('context');
		session()->put('context', ['school' => $id]);

		$mode = $show ? config('global.show') : config('global.edit');
		$school = School::findOrFail($id);
		$baseRight = sprintf("schools.%s", $show ? "show" : "edit");

		if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
			$users = User::orderBy('name')->get()
				->map(function ($user) {
					$collect =
						(auth()->user()->getKey() == $user->getKey()) ||
						($user->hasRole(RoleName::SCHOOL->value))
					;
					if (!$collect)
						return null;

					return [
						'id' => $user->getKey(),
						'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
					];
				})
				->reject(fn($value) => $value === null)
				->toArray();
			return view('schools.edit', compact('school', 'users', 'mode'));
		} elseif (auth()->user()->can($baseRight) || auth()->user()->allowed($school))
			return view('schools.edit', compact('school', 'mode'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для редактирования / просмотра записи образовательного учреждения'));
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
	public function update(StoreSchoolRequest $request, $id): RedirectResponse {
		$school = School::findOrFail($id);
		$oldStatus = $school->status;
		$name = $school->name;
		$school->update($request->all());
		$newStatus = $school->status;

		auth()->user()->allow($school);

		$school->user->notify(new UpdateSchool($school));
		if ($oldStatus != $newStatus && $newStatus == ActiveStatus::ACTIVE->value)
			event(new UpdateSchoolTaskEvent($school));

		session()->put('success', "Анкета образовательного учреждения \"{$name}\" обновлена");
		return redirect()->route('schools.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $school
	 * @return bool
	 */
	public function destroy(Request $request, int $school): bool {
		if ($school == 0) {
			$id = $request->id;
		} else
			$id = $school;

		$school = School::findOrFail($id);
		$name = $school->name;

		auth()->user()->disallow($school);
		$school->user->disallow($school);

		$school->delete();

		event(new ToastEvent('success', '', "Образовательное учреждение '{$name}' удалено"));
		return true;
	}
}