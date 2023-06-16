<?php

namespace App\Http\Controllers\contracts;

use App\Models\Contract;
use Illuminate\Http\Request;
use DateTime;

class StepContract implements Step {
	public function isBrowse(): bool {
		return false;
	}

	public function getBrowseData(Request $request) {
		return null;
	}

	public function getTitle(): string {
		return 'Реквизиты и скан договора';
	}

	public function getStoreRules(): array {
		return [
			'number' => 'required',
			'sealed' => 'required',
			'start' => 'required',
			'finish' => 'required',
		];
	}

	public function getStoreAttributes(): array {
		return [
			'number' => 'Номер договора',
			'sealed' => 'Дата подписания договора',
			'start' => 'Дата начала практики',
			'finish' => 'Дата завершения практики',
		];
	}

	public function run(Request $request) {
		$mode = config('global.create');
		$buttons = intval($request->buttons);

		$heap = session('heap') ?? [];
		$heap[$this->getContext()] = '';
		session()->put('heap', $heap);

		return view('contracts.steps.contract', compact('mode', 'buttons', 'heap'));
	}

	public function store(Request $request): bool {
		$heap = session('heap') ?? [];
		$heap['number'] = $request->number;
		$heap['sealed'] = new DateTime($request->sealed);
		$heap['start'] = new DateTime($request->start);
		$heap['finish'] = new DateTime($request->finish);
		$heap['scan'] = Contract::uploadScan($request);

		$heap['contract'] = sprintf("№ %s от %s", $heap['number'], $heap['sealed']->format('d.m.Y'));
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
