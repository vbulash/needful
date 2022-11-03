<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SettingsController extends Controller {
	public function notifications(Request $request) {
		$nstates = Redis::get('settings.notifications');
		return view('settings.nstates', compact('nstates'));
	}

	public function notificationsStore(Request $request) {
		Redis::set('settings.notifications', $request->states);
		session()->put('success', 'Настройки уведомлений сохранены');
		return redirect()->route('settings.notifications');
	}
}
