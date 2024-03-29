<?php

namespace App\Http\Controllers\orders;

use App\Models\School;
use Illuminate\Http\Request;

class StepSpecialties implements Step {
	public function isBrowse(): bool {
		return false;
	}

	public function getBrowseData(Request $request) {
		return null;
	}

	public function getTitle(): string {
		return 'Выбор специальностей';
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
		$heap[$this->getContext()] = '';
		session()->put('heap', $heap);

		$school = School::findOrFail($heap['school']);
		$specialties = [];
		$school->specialties()->each(function ($item) use (&$specialties) {
			$specialties[] = [
				'id' => $item->specialty->getKey(),
				'name' => $item->specialty->getTitle(),
			];
		});
		usort($specialties, fn ($a, $b) => $a['name'] < $b['name'] ? -1 : ($a['name'] > $b['name'] ? 1 : 0));
		$specialties = json_encode($specialties);

		return view('orders.steps.specialties', compact('mode', 'buttons', 'heap', 'specialties'));
	}

	public function store(Request $request): bool {
		$heap = session('heap') ?? [];
		$heap['specialties'] = json_decode($request->specs);
		$heap[$this->getContext()] = (count($heap['specialties']) == 1 ? $heap['specialties'][0]->text : '[заполнено]');
		session()->put('heap', $heap);
		return true;
	}
	/**
	 * @return string
	 */
	public function getContext(): string {
		return 'order.specialty';
	}
}
