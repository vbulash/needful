<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Requests\StoreFspecialtyRequest;
use App\Http\Requests\UpdateFspecialtyRequest;
use App\Models\Faculty;
use App\Models\Fspecialty;
use App\Models\School;
use App\Models\Specialty;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FspecialtyController extends Controller {
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request) {
		$context = session('context');
		$query = School::findOrFail($context['school'])->fspecialties()->get();

		return Datatables::of($query)
			->addColumn('name', fn($fspecialty) => $fspecialty->specialty->name)
			->addColumn('action', function ($fspecialty) {
				$editRoute = route('fspecialties.edit', ['fspecialty' => $fspecialty->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('fspecialties.show', ['fspecialty' => $fspecialty->getKey(), 'sid' => session()->getId()]);
				$items = [];

				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				$items[] = ['type' => 'item', 'click' => "clickDelete({$fspecialty->getKey()}, '{$fspecialty->specialty->name}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function index(Request $request) {
		$context = session('context');
		unset($context['fspecialty']);
		session()->put('context', $context);

		$school = School::findOrFail($context['school']);
		$count = $school->fspecialties()->count();

		return view('fspecialties.index', compact('count'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return Application|Factory|View
	 */
	public function create(Request $request) {
		$mode = config('global.create');
		$context = session('context');
		$school = School::findOrFail($context['school']);

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

		return view('fspecialties.create', compact('school', 'specialties', 'mode'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function store(StoreFspecialtyRequest $request) {
		$context = session('context');
		$school = School::findOrFail($context['school']);

		if ($request->has('specialty') && isset($request->specialty)) { // Новая специальность
			// Сначала добавить новую специальность в список
			$specialty = Specialty::create([
				'name' => $request->specialty
			]);
			$specialty->save();
			$created = true;
		} else { // Выбор из существующих
			$specialty = Specialty::findOrFail($request->specialty_id);
			$created = false;
		}

		// Затем добавляем эту специальность в список специальностей образовательного учреждения
		$fspecialty = new Fspecialty();
		$fspecialty->specialty()->associate($specialty);
		$fspecialty->school()->associate($school);
		$fspecialty->save();
		$name = $fspecialty->name;

		session()->put('success', $created ?
			"Специальность \"{$name}\" добавлена" :
			"Список специальностей образовательного учреждения изменён"
		);

		return redirect()->route('fspecialties.index', ['sid' => session()->getId()]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return Application|Factory|View
	 */
	public function show(Request $request, int $id) {
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
	public function edit(Request $request, int $id, bool $show = false) {
		$context = session('context');
		$context['fspecialty'] = $id;
		session()->put('context', $context);

		$mode = $show ? config('global.show') : config('global.edit');
		$fspecialty = Fspecialty::findOrFail($id);

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

		return view('fspecialties.edit', compact('fspecialty', 'specialties', 'mode'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UpdateFspecialtyRequest $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function update(UpdateFspecialtyRequest $request, $id) {
		$fspecialty = Fspecialty::findOrFail($id);
		$school = $fspecialty->school;

		$out = [];
		if ($request->has('specialty') && isset($request->specialty)) { // Новая специальность
			// Сначала добавить новую специальность в список
			$specialty = Specialty::create([
				'name' => $request->specialty
			]);
			$specialty->save();
			$name = $specialty->name;
			$out[] = "Специальность \"{$name}\" добавлена";
		} else { // Выбор из существующих
			$specialty = Specialty::findOrFail($request->specialty_id);
		}
		$fspecialty->specialty()->associate($specialty);
		$fspecialty->school()->associate($school);
		$fspecialty->update();
		$out[] = "Список специальностей образовательного учреждения обновлён";

		session()->put('success', implode('<br/>', $out));
		return redirect()->route('fspecialties.index', ['sid' => session()->getId()]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $fspecialty
	 * @return bool
	 */
	public function destroy(Request $request, int $fspecialty) {
		if ($fspecialty == 0) {
			$id = $request->id;
		} else
			$id = $fspecialty;

		$fspecialty = Fspecialty::findOrFail($id);
		$name = $fspecialty->specialty->name;
		$fspecialty->delete();

		event(new ToastEvent('success', '', "Специальность '{$name}' удалена из списка специальностей образовательного учреждения"));
		return true;
	}
}