<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEarlyWarningsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class EarlyWarningsController extends Controller {
	public function warnings(Request $request) {
		$mode = config('global.edit');
		$early = Redis::get('settings.early');
		return view('settings.early', compact('mode', 'early'));
	}

	public function warningsStore(UpdateEarlyWarningsRequest $request) {
		Redis::set('settings.early',
			json_encode([
				'cancel' => $request->cancel,
				'last' => $request->last
			])
		);
		session()->put('success', 'Настройки писем сохранены');
		return redirect()->route('settings.early');
	}
}
