<?php

namespace App\Http\Controllers\orders;

use App\Models\ActiveStatus;
use App\Models\School;
use App\Models\SchoolType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StepSchool implements Step {
	public function isBrowse(): bool {
		return true;
	}

	public function getBrowseData(Request $request) {
		$query = School::all()->where('status', ActiveStatus::ACTIVE->value);

		return DataTables::of($query)
			->addColumn('short', fn($school) => $school->short)
			->addColumn('type', fn($school) => SchoolType::getName($school->type))
			->addColumn('action', function ($school) use ($request) {
				$selectRoute = route('orders.steps.next', [
					'school' => $school->getKey()
				]);
				$items = [];
				$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Параметры заявки'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function getTitle(): string {
		return 'Образовательное учреждение';
	}

	public function getStoreRules(): array {
		return [];
	}

	public function getStoreAttributes(): array {
		return [];
	}

	public function run(Request $request) {
		$mode = config('global.create');
		$buttons = intval($request->buttons);
		$count = School::where('status', ActiveStatus::ACTIVE)->count();

		session()->forget('context');
		$heap = [];
		$heap[$this->getContext()] = '';
		session()->put('heap', $heap);

		return view('orders.steps.school', compact('mode', 'buttons', 'count'));
	}

	public function store(Request $request): bool {
		$heap = session('heap') ?? [];
		$heap['school'] = $request->school;
		session()->put('heap', $heap);

		return true;
	}
	/**
	 * @return string
	 */
	public function getContext(): string {
		return 'school';
	}
}
