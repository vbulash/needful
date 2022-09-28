<?php

namespace App\Http\Controllers;

use App\Events\UnreadCountEvent;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TaskController extends Controller
{
	public function read(Request $request)
	{
		$task = Task::findOrFail($request->message);
		$read = $task->read;
		$task->update(['read' => !$read]);

		event(new UnreadCountEvent());
    }

	public function link(Request $request)
	{
		$context = json_decode($request->context, true);
		$context['chain'] = true;
		session()->put('context', $context);

		return $request->route;
	}
}
