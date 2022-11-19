<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreEspecialtyRequest;
use App\Http\Requests\StoreFspecialtyRequest;
use App\Http\Requests\UpdateEspecialtyRequest;
use App\Models\Especialty;
use App\Models\Fspecialty;
use App\Models\Internship;
use App\Models\School;
use App\Models\Specialty;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use \Exception;

class EspecialtyController extends Controller
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
		$context = session('context');
		$query = Internship::findOrFail($context['internship'])->especialties()->get();

		return Datatables::of($query)
			->addColumn('name', fn ($especialty) => $especialty->specialty->name)
			->addColumn('action', function ($especialty) {
				$editRoute = route('especialties.edit', ['especialty' => $especialty->getKey()]);
				$showRoute = route('especialties.show', ['especialty' => $especialty->getKey()]);
				$actions = '';

				$actions .=
					"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left ms-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
					"<i class=\"fas fa-edit\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left ms-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left ms-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$especialty->getKey()}, '{$especialty->specialty->name}')\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function index(Request $request)
	{
		$context = session('context');
		unset($context['especialty']);
		session()->put('context', $context);

		$internship = Internship::findOrFail($context['internship']);
		$count = $internship->especialties()->count();

		return view('especialties.index', compact('count'));
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function create(Request $request)
	{
		$mode = config('global.create');

		$temp = Specialty::all()
			->sortBy('name');
		$specialties = [];
		foreach ($temp as $item) {
			$specialties[] = [
				'id' => $item->getKey(),
				'text' => $item->name,
				'federal' => $item->federal
			];
		}
		$specialties = json_encode($specialties);

		return view('especialties.create', compact('specialties', 'mode'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param StoreEspecialtyRequest $request
	 * @return RedirectResponse
	 */
	public function store(StoreEspecialtyRequest $request)
	{
		$context = session('context');
		$internship = Internship::findOrFail($context['internship']);

		if ($request->has('specialty') && isset($request->specialty)) {    // Новая специальность
			// Сначала добавить новую специальность в список
			$specialty = Specialty::create([
				'name' => $request->specialty
			]);
			$specialty->save();
			$created = true;
		} else {    // Выбор из существующих
			$specialty = Specialty::findOrFail($request->specialty_id);
			$created = false;
		}

		// Затем добавляем эту специальность в список специальностей работодателя
		$especialty = new Especialty();
		$especialty->specialty()->associate($specialty);
		$especialty->internship()->associate($internship);
		$especialty->count = $request->count;
		$especialty->save();
		$name = $especialty->name;

		session()->put('success', $created ?
			"Специальность \"{$name}\" добавлена" :
			"Список специальностей по практике работодателя изменён"
		);

		return redirect()->route('especialties.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(Request $request, int $id)
	{
		return $this->edit($request, $id, true);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @param bool $show
	 * @return Application|Factory|View
	 */
	public function edit(Request $request, int $id, bool $show = false)
	{
		$context = session('context');
		$context['especialty'] = $id;
		session()->put('context', $context);

		$mode = $show ? config('global.show') : config('global.edit');
		$especialty = Especialty::findOrFail($id);

		$temp = Specialty::all()
			->sortBy('name');
		$specialties = [];
		foreach ($temp as $item) {
			$specialties[] = [
				'id' => $item->getKey(),
				'text' => $item->name,
				'federal' => $item->federal
			];
		}
		$specialties = json_encode($specialties);

		return view('especialties.edit', compact('especialty', 'specialties', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateEspecialtyRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateEspecialtyRequest $request, $id)
	{
		$especialty = Especialty::findOrFail($id);
		$internship = $especialty->internship;

		$out = [];
		if ($request->has('specialty') && isset($request->specialty)) {    // Новая специальность
			// Сначала добавить новую специальность в список
			$specialty = Specialty::create([
				'name' => $request->specialty
			]);
			$specialty->save();
			$name = $specialty->name;
			$out[] = "Специальность \"{$name}\" добавлена";
		} else {	// Выбор из существующих
			$specialty = Specialty::findOrFail($request->specialty_id);
		}
		$especialty->specialty()->associate($specialty);
		$especialty->internship()->associate($internship);
		$especialty->count = $request->count;
		$especialty->update();
		$out[] = "Список специальностей практики работодателя обновлён";
		session()->put('success', implode('<br/>', $out));

		return redirect()->route('especialties.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $especialty
	 * @return bool
	 */
	public function destroy(Request $request, int $especialty)
	{
		if ($especialty == 0) {
			$id = $request->id;
		} else $id = $especialty;

		$especialty = Especialty::findOrFail($id);
		$name = $especialty->specialty->name;
		$especialty->delete();

		event(new ToastEvent('success', '', "Специальность '{$name}' удалена из списка специальностей практики работодателя"));
		return true;
	}
}
