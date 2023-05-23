<?php

namespace App\Http\Controllers\contracts;

use App\Models\ActiveStatus;
use App\Models\Employer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StepEmployer implements Step {
	public function isBrowse(): bool {
		return true;
	}

	public function getBrowseData(Request $request) {
		$query = Employer::all()
			->where('status', ActiveStatus::ACTIVE->value)
			->sortBy('short');

		return DataTables::of($query)
			->addColumn('short', fn($employer) => $employer->short)
			->addColumn('action', function ($employer) use ($request) {
				$selectRoute = route('contracts.steps.next', [
					'employer' => $employer->getKey()
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
		return 'Работодатель';
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
		$count = Employer::where('status', ActiveStatus::ACTIVE)->count();

		$heap = session('heap') ?? [];
		$heap['contract'] = '';
		$heap[$this->getContext()] = '';
		session()->put('heap', $heap);

		return view('contracts.steps.employer', compact('mode', 'buttons', 'count'));
	}

	public function store(Request $request): bool {
		$heap = session('heap') ?? [];
		$heap['contract'] = '';
		$heap['employer'] = $request->employer;
		session()->put('heap', $heap);

		return true;
	}
	/**
	 * @return string
	 */
	public function getContext(): string {
		return 'employer';
	}
}