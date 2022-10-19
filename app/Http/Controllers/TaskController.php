<?php

namespace App\Http\Controllers;

use App\Events\EventType;
use App\Events\UnreadCountEvent;
use App\Models\Task;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

	public function archive(Request $request): Response|Application|ResponseFactory
	{
		$task = Task::findOrFail($request->message);
		$task->update([
			'read' => true,
			'archive' => !$task->archive
		]);

		event(new UnreadCountEvent());

		return response(status: 200);
	}

	public function dispatcher(Request $request): Response|RedirectResponse|Application|ResponseFactory|null
	{
		$task = $request->task;
		$read = $request->read == 'true';
		$archive = $request->archive == 'true';
		if ($archive) $read = true;

		$updated = (Task::findOrFail($task))->update([
			'read' => $read,
			'archive' => $archive,
		]);
		if ($updated)
			event(new UnreadCountEvent());
		return response(content: $updated, status: 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @return bool
	 */
	public function destroy(Request $request): bool
	{
		$task = Task::findOrFail($request->id);
		$task->delete();
		return true;
	}
}
