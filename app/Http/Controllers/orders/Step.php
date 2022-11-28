<?php

namespace App\Http\Controllers\orders;

use App\Models\Titleable;
use Illuminate\Http\Request;

interface Step extends Titleable
{
	public function isBrowse(): bool;

	public function getBrowseData(Request $request);

	public function getTitle(): string;

	public function getContext(): string;

	public function getStoreRules(): array;

	public function getStoreAttributes(): array;

	public function run(Request $request);

	public function store(Request $request): bool;
}
