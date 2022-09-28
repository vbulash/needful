<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InboxController extends Controller
{
	public function index(): Factory|View|Application
	{
		$tasks = Task::all()->sortBy('created_at', descending: true);
		return view('inbox.inbox', compact('tasks'));
    }
}
