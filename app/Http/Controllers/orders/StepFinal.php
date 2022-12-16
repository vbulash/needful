<?php

namespace App\Http\Controllers\orders;

use App\Models\School;
use Illuminate\Http\Request;

class StepFinal implements Step {
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
		$mode = config('global.show');
		$buttons = intval($request->buttons);
		$heap = session('heap') ?? [];

		$total = [];
		$temp = School::findOrFail($heap['school']);
		$total['school'] = $temp->getTitle();
		//
		$total['name'] = $heap['name'];
		$total['start'] = $heap['start'];
		$total['end'] = $heap['end'];
		$total['description'] = $heap['description'];
		//
		$temp = [];
		foreach ($heap['specialties'] as $item)
			$temp[] = sprintf("%s (%d)", $item->text, $item->quantity);
		$total['specialties'] = implode(', ', $temp);
		//
		$temp = [];
		foreach ($heap['employers'] as $item)
			$temp[] = $item->text;
		$total['employers'] = implode(', ', $temp);

		return view('orders.steps.final', compact('mode', 'buttons', 'total'));
	}

	public function store(Request $request): bool {
		return true;
	}
	/**
	 * @return string
	 */
	public function getContext(): string {
		return '';
	}
}
