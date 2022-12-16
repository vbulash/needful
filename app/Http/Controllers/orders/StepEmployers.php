<?php

namespace App\Http\Controllers\orders;

use App\Models\Employer;
use App\Models\School;
use Illuminate\Http\Request;

class StepEmployers implements Step {
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

		$employers = [];
		Employer::all()->each(function ($item) use (&$employers) {
			$employers[] = [
				'id' => $item->getKey(),
				'name' => $item->getTitle(),
			];
		});
		usort($employers, fn ($a, $b) => $a['name'] < $b['name'] ? -1 : ($a['name'] > $b['name'] ? 1 : 0));
		$employers = json_encode($employers);

		return view('orders.steps.employers', compact('mode', 'buttons', 'heap', 'employers'));
	}

	public function store(Request $request): bool {
		$heap = session('heap') ?? [];
		$heap['employers'] = json_decode($request->emps);
		session()->put('heap', $heap);
		return true;
	}
	/**
	 * @return string
	 */
	public function getContext(): string {
		return '';
	}
}
