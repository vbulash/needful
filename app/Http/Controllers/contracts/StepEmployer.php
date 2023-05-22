<?php

namespace App\Http\Controllers\contracts;

use App\Models\Employer;
use App\Models\School;
use Illuminate\Http\Request;

class StepEmployer implements Step {
	public function isBrowse(): bool {
		return false;
	}

	public function getBrowseData(Request $request) {
		return null;
	}

	public function getTitle(): string {
		return 'Уведомление работодателям';
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
		$heap = session('heap') ?? [];
		$specs_school = collect($heap['specialties'])->pluck('id')->toArray();

		$employers = [];
		Employer::fullAll()->each(function ($item) use (&$employers, $specs_school) {
			$specs_employer = [];
			$item->specialties()->each(function ($item_specialty) use (&$specs_employer) {
				$specs_employer[] = $item_specialty->specialty->getKey();
			});
			$specs = array_intersect($specs_school, $specs_employer);
			if (count($specs) > 0)
				$employers[] = [
					'id' => $item->getKey(),
					'name' => $item->getTitle(),
				];
		});
		usort($employers, fn($a, $b) => $a['name'] < $b['name'] ? -1 : ($a['name'] > $b['name'] ? 1 : 0));
		$employers = json_encode($employers);

		$heap = session('heap') ?? [];
		$heap[$this->getContext()] = '';
		session()->put('heap', $heap);

		return view('orders.steps.employers', compact('mode', 'buttons', 'heap', 'employers'));
	}

	public function store(Request $request): bool {
		$heap = session('heap') ?? [];
		$heap['employers'] = json_decode($request->emps);
		$heap[$this->getContext()] = (count($heap['employers']) == 1 ? $heap['employers'][0]->text : '[заполнено]');
		session()->put('heap', $heap);
		return true;
	}
	/**
	 * @return string
	 */
	public function getContext(): string {
		return 'order.employer';
	}
}