<?php

namespace App\Http\Controllers\orders;

use App\Models\ActiveStatus;
use App\Models\School;
use App\Models\SchoolType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StepOrder implements Step {
	public function isBrowse(): bool {
		return false;
	}

	public function getBrowseData(Request $request) {
		return null;
	}

	public function getTitle(): string {
		return 'Параметры заявки';
	}

	public function getStoreRules(): array {
		return [
			'name' => 'required',
			'start' => 'required',
			'end' => 'required'
		];
	}

	public function getStoreAttributes(): array {
		return [
			'name' => 'Название практики',
			'start' => 'Дата начала',
			'end' => 'Дата завершения'
		];
	}

	public function run(Request $request) {
		$mode = config('global.create');
		$buttons = intval($request->buttons);
		$heap = session('heap') ?? [];

		return view('orders.steps.order', compact('mode', 'buttons', 'heap'));
	}

	public function store(Request $request): bool {
		$heap = session('heap') ?? [];
		$heap['name'] = $request->name;
		$heap['start'] = $request->start;
		$heap['end'] = $request->end;
		session()->put('heap', $heap);
		return true;
	}
	/**
	 * @return string
	 */
	public function getContext(): string {
		return '[заполнены]';
	}
}
