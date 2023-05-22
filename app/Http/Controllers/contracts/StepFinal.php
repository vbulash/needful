<?php

namespace App\Http\Controllers\contracts;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StepFinal implements Step {
	public function isBrowse(): bool {
		return false;
	}

	public function getBrowseData(Request $request) {
		return null;
	}

	public function getTitle(): string {
		return 'Подтверждение информации';
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
		$total['place'] = $heap['place'];
		$total['description'] = $heap['description'];
		//
		$temp = [];
		foreach ($heap['specialties'] as $item)
			$temp[] = sprintf("%s (%d)", $item->text, $item->quantity);
		$total['specialties'] = implode(', ', $temp);
		//
		$temp = []; foreach ($heap['employers'] as $item)
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