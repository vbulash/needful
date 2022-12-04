<?php

namespace App\Http\Controllers;

use App\Console\ProcessTrainings;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class MainController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Application|Factory|View
	 */
	public function index(): Application|Factory|View {
		session()->forget('heap');
		session()->forget('continue');
		return view('main');
	}
}
