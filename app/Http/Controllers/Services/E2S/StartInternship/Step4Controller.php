<?php

namespace App\Http\Controllers\Services\E2S\StartInternship;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Models\ActiveStatus;
use App\Models\Student;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;

class Step4Controller extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request): JsonResponse
	{
		$query = Student::where('status', ActiveStatus::ACTIVE->value);

		return Datatables::of($query)
			->editColumn('fio', fn ($student) => $student->getTitle())
			->editColumn('birthdate', fn ($student) => $student->birthdate->format('d.m.Y'))
			->addColumn('action', function ($student) {
				$showRoute = route('e2s.start_internship.step4.show', ['student' => $student->id, 'sid' => session()->getId()]);
				$actions =
					"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
					"<i class=\"fas fa-eye\"></i>\n" .
					"</a>\n";
				return $actions;
			})
			->make(true);
	}

	// Выбор
	public function select(Request $request): RedirectResponse
	{
		$context = session('context');
		$context['ids'] = json_decode($request->ids);
		$context['names'] = $request->names;
		session()->put('context', $context);

		return redirect()->route('e2s.start_internship.step4b');
	}

	// Просмотр карточки стажировки
	public function showStudent(int $id): Factory|View|Application
	{
		$student = Student::findOrFail($id);
		return view('services.e2s.start_internship.show-student', compact('student'));
	}

	//
	public function run(): Factory|View|Application
	{
		$context = session('context');
		unset($context['student']);

		$view = 'services.e2s.start_internship.step4';
		$count = Student::all()->count();

		if ($count == 0) {
			event(new ToastEvent('info', '',
				'Нет студентов. Необходимо их создать'));
			//return redirect()->route('dashboard', ['sid' => session()->getId()]);
		}

		return view($view, compact('count'));
	}
}
