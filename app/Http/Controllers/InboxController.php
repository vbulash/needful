<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RoleName;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InboxController extends Controller
{
	public function index(): Factory|View|Application
	{
		$tasks = Task::getMyTasks(Task::all()->where('archive', false))
			->sortBy('created_at', descending: true);
		return view('inbox.inbox', compact('tasks'));
    }

	public function archive(): Factory|View|Application
	{
		$tasks = Task::getMyTasks(Task::all()->where('archive', true))
			->sortBy('created_at', descending: true);
		return view('inbox.archive', compact('tasks'));
	}
}
