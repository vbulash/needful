<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\UpdateSpecialtyRequest;
use App\Models\Specialty;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

/**
 * Контроллер специальностей
 */
class SpecialtyController extends Controller
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
		$query = Specialty::all()
			->sortBy('name');

		return Datatables::of($query)
			->addColumn('federal', fn($specialty) => $specialty->federal ? 'Федеральный справочник' : 'Внесена вручную')
			->addColumn('action', function ($specialty) {
				$editRoute = route('specialties.edit', ['specialty' => $specialty->id, 'sid' => session()->getId()]);
				$showRoute = route('specialties.show', ['specialty' => $specialty->id, 'sid' => session()->getId()]);
				$actions = '';

				if (!$specialty->federal)
					$actions .=
						"<a href=\"$editRoute\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				$actions .=
					"<a href=\"$showRoute\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				if (!$specialty->federal)
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-5\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$specialty->getKey()}, '{$specialty->name}')\">\n" .
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
		$count = Specialty::all()->count();
		return view('dictionaries.specialties.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Application|Factory|View
	 */
	public function create()
	{
		$mode = config('global.create');
		return view('dictionaries.specialties.create', compact('mode'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreSpecialtyRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreSpecialtyRequest $request)
	{
		$data = $request->except('_token');
		$data['federal'] = false;
		$specialty = Specialty::create($data);
		$specialty->save();

		$name = $specialty->name;
		session()->put('success', "Специальность \"{$name}\" создана");
		return redirect()->route('specialties.index', ['sid' => session()->getId()]);
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
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(int $id, bool $show = false)
	{
		$mode = $show ? config('global.show') : config('global.edit');
		$specialty = Specialty::findOrFail($id);
		return view('dictionaries.specialties.edit', compact('specialty', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateSpecialtyRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateSpecialtyRequest $request, int $id)
	{
		$specialty = Specialty::findOrFail($id);
		$name = $specialty->name;
		$specialty->update($request->except(['_token']));

		session()->put('success', "Специальность \"{$name}\" изменена");
		return redirect()->route('specialties.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $specialty
	 * @return bool
	 */
	public function destroy(Request $request, int $specialty)
	{
		if ($specialty == 0) {
			$id = $request->id;
		} else $id = $specialty;

		$specialty = Specialty::findOrFail($id);
		$name = $specialty->name;
		$specialty->delete();

		event(new ToastEvent('success', '', "Специальность \"{$name}\" удалена"));
		return true;
	}
}
