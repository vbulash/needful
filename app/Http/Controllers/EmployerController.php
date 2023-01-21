<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Events\UpdateEmployerTaskEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Http\Requests\StoreEmployerRequest;
use App\Models\ActiveStatus;
use App\Models\Employer;
use App\Models\Right;
use App\Models\User;
use App\Notifications\NewEmployer;
use App\Notifications\UpdateEmployer;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmployerController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse {
		$context = session('context');
		if (isset($context['chain']))
			$query = Employer::where('id', $context['employer']);
		else
			$query = Employer::all();

		return Datatables::of($query)
			->editColumn('link', fn($employer) => $employer->user->name)
			->addColumn('action', function ($employer) use ($context) {
				$editRoute = route('employers.edit', ['employer' => $employer->id]);
				$showRoute = route('employers.show', ['employer' => $employer->id]);
				$selectRoute = route('employers.select', ['employer' => $employer->id]);
				$items = [];

				if (!isset($context['chain']))
					if (auth()->user()->can('employers.edit'))
						$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];

				if (auth()->user()->can('employers.show'))
					$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];

				if (!isset($context['chain']))
					if (auth()->user()->can('employers.delete'))
						$items[] = ['type' => 'item', 'click' => "clickDelete({$employer->id}, '{$employer->short}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];

				if ($employer->status == ActiveStatus::ACTIVE->value) {
					$items[] = ['type' => 'divider'];
					$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Специальности'];
					$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Отзывы на заявки'];
				}

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function select(int $id): RedirectResponse {
		$employer = Employer::findOrFail($id);
		$context = session('context');
		if (!isset($context['chain'])) {
			session()->forget('context');
			session()->put('context', ['employer' => $employer->getKey()]);
		}

		return redirect()->route('employer.specialties.index', ['employer' => $id]);
	}

	public function getClear(): RedirectResponse {
		session()->forget('context');
		return redirect()->route('employers.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function index(): View|Factory|RedirectResponse|Application {
		$context = session('context');
		if (isset($context['chain'])) {
			$count = 1;
		} else {
			session()->forget('context');
			$count = Employer::all()->count();
		}
		if (auth()->user()->can('employers.list')) {
			return view('employers.index', compact('count'));
		} elseif (auth()->user()->can('employers.create')) {
			return redirect()->route('employers.create');
		} else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи работодателя'));
			return redirect()->route('dashboard');
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function create(): View|Factory|RedirectResponse|Application {
		$mode = config('global.create');
		$baseRight = "employers.create";
		if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
			$users = User::orderBy('name')->get()
				->map(function ($user) {
					$collect =
						(auth()->user()->getKey() == $user->getKey()) ||
						($user->hasRole(RoleName::EMPLOYER->value));
					if (!$collect)
						return null;

					return [
						'id' => $user->getKey(),
						'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
					];
				})
				->reject(fn($value) => $value === null)
				->toArray();
			return view('employers.create', compact('users', 'mode'));
		} elseif (auth()->user()->can($baseRight))
			return view('employers.create', compact('mode'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для создания записи работодателя'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreEmployerRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreEmployerRequest $request): RedirectResponse {
		$employer = Employer::create($request->all());
		$employer->save();
		$name = $employer->name;

		auth()->user()->allow($employer);

		$employer->user->notify(new NewEmployer($employer));

		session()->put('success', "Работодатель \"{$name}\" создан");
		return redirect()->route('employers.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Factory|View|Application|RedirectResponse
	 */
	public function show(int $id): Factory|View|Application|RedirectResponse {
		return $this->edit($id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View|RedirectResponse
	 */
	public function edit(int $id, bool $show = false): View|Factory|RedirectResponse|Application {
		$mode = $show ? config('global.show') : config('global.edit');
		$employer = Employer::findOrFail($id);
		$baseRight = sprintf("employers.%s", $show ? "show" : "edit");
		$right = sprintf("%s.%d", $baseRight, $employer->getKey());

		if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
			$users = User::orderBy('name')->get()
				->map(function ($user) {
					$collect =
						(auth()->user()->getKey() == $user->getKey()) ||
						($user->hasRole(RoleName::EMPLOYER->value));
					if (!$collect)
						return null;

					return [
						'id' => $user->getKey(),
						'name' => sprintf("%s (роль %s)", $user->name, $user->roles()->first()->name)
					];
				})
				->reject(fn($value) => $value === null)
				->toArray();
			return view('employers.edit', compact('employer', 'users', 'mode'));
		} elseif (auth()->user()->can($baseRight) || auth()->user()->can($right))
			return view('employers.edit', compact('employer', 'mode'));
		else {
			event(new ToastEvent('info', '', 'Недостаточно прав для редактирования / просмотра записи работодателя'));
			return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param StoreEmployerRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(StoreEmployerRequest $request, int $id): RedirectResponse {
		$employer = Employer::findOrFail($id);
		$name = $employer->name;
		$oldStatus = $employer->status;
		$employer->update($request->all());
		$newStatus = $employer->status;

		auth()->user()->allow($employer);

		$employer->user->notify(new UpdateEmployer($employer));
		if ($oldStatus != $newStatus && $newStatus == ActiveStatus::ACTIVE->value)
			event(new UpdateEmployerTaskEvent($employer));

		session()->put('success', "Анкета работодателя \"{$name}\" обновлена");
		return redirect()->route('employers.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $employer
	 * @return bool
	 */
	public function destroy(Request $request, int $employer): bool {
		if ($employer == 0) {
			$id = $request->id;
		} else
			$id = $employer;

		$employer = Employer::findOrFail($id);
		$name = $employer->name;

		auth()->user()->disallow($employer);
		$employer->user->disallow($employer);

		$employer->delete();

		event(new ToastEvent('success', '', "Работодатель '{$name}' удалён"));
		return true;
	}
}
