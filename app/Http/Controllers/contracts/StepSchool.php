<?php

namespace App\Http\Controllers\contracts;

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
		$query = School::all()
			->where('status', ActiveStatus::ACTIVE->value)
			->sortBy('short');

		return DataTables::of($query)
			->addColumn('short', fn($school) => $school->short)
			->addColumn('type', fn($school) => SchoolType::getName($school->type))
			->addColumn('action', function ($school) use ($request) {
				$selectRoute = route('contracts.steps.next', [
					'school' => $school->getKey()
				]);
				$actions =
					"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
					"<i class=\"fas fa-check\"></i>\n" .
					"</a>\n";
				return $actions;
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

		return view('contracts.steps.school', compact('mode', 'buttons', 'count'));
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