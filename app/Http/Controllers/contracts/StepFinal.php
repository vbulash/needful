<?php

namespace App\Http\Controllers\contracts;

use App\Models\Employer;
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
		$temp = Employer::findOrFail($heap['employer']);
		$total['employer'] = $temp->getTitle();
		//
		$total['number'] = $heap['number'];
		$total['sealed'] = $heap['sealed']->format('d.m.Y');
		$total['start'] = $heap['start']->format('d.m.Y');
		$total['finish'] = $heap['finish']->format('d.m.Y');
		$total['scan'] = isset($heap['scan']) ? 'Да' : 'Нет';

		return view('contracts.steps.final', compact('mode', 'buttons', 'total'));
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
