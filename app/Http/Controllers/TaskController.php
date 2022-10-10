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
//		$type = EventType::MESSAGE->value;
		if ($request->has('data')) {
			$data = json_decode($request->data, true);
			$task = $request->task;

			if ($data['eventType'] == EventType::INVITE_ATTENDEES->value) {
				$route = match ($data['buttonType']) {
					'accept' => 'history.accept',
					'reject' => 'history.reject',
					default => null
				};
				if (!$route) return null;

				$history = $data['history'];
				$trainee = $data['trainee'];
				return redirect()->route($route, ['history' => $history, 'trainee' => $trainee, 'task' => $task]);
//				return Route::dispatch(Request::create($route, 'POST', ['history' => $history, 'trainee' => $trainee, 'task' => $task]));
			}
		}
		return response(status: 204);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $task
	 * @return bool
	 */
	public function destroy(Request $request, int $task): bool
	{
		if ($task == 0) {
			$id = $request->id;
		} else $id = $task;

		$task = Task::findOrFail($id);
		$task->delete();
		return true;
	}
}
