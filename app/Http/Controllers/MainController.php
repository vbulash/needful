<?php

namespace App\Http\Controllers;

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
		//event(new TaskEvent('Описание задачи...', route('dashboard'), auth()->user(), null));
		return view('main');
	}
}
