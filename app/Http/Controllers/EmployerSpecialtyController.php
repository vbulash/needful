<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Models\Employer;
use App\Models\EmployerSpecialty;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployerSpecialtyController extends Controller {
	public function getData(int $employer) {
		$query = Employer::findOrFail($employer)->specialties();

		return DataTables::of($query)
			->editColumn('id', fn($employer_specialty) => $employer_specialty->specialty->getKey())
			->addColumn('specialty', fn($employer_specialty) => $employer_specialty->specialty->getTitle())
			->addColumn('action', function ($employer_specialty) use ($employer) {
				$id = $employer_specialty->specialty->getKey();
				$name = $employer_specialty->specialty->getTitle();
				$actions = '';

				$actions .=
					"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left me-1\" " .
					"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" " .
					"onclick=\"clickDelete({$employer_specialty->getKey()}, '{$name}', {$id})\">\n" .
					"<i class=\"fas fa-trash-alt\"></i>\n" .
					"</a>\n";

				return $actions;
			})
			->make(true);
	}

	public function index(int $employer) {
		$context = session('context');
		unset($context['employer.specialty']);
		session()->put('context', $context);

		$employer = Employer::findOrFail($employer);
		$count = $employer->specialties()->count();
		$selected = [];
		$employer->specialties()->each(function ($specialty) use (&$selected) {
			$id = $specialty->specialty->getKey();
			$text = $specialty->specialty->getTitle();
			$selected[$id] = $text;
		});
		$enabled = [];
		Specialty::all()->each(function ($specialty) use (&$enabled, $selected) {
			$id = $specialty->getKey();
			$text = $specialty->getTitle();
			if (array_key_exists($id, $selected))
				return;
			$enabled[$id] = $text;
		});
		return view('employers.specialties.index', compact('count', 'employer', 'selected', 'enabled'));
	}

	public function create() {
		//
	}

	public function store(Request $request) {
		$context = session('context');
		$employer = Employer::findOrFail($context['employer']);

		$id = $request->id;
		$text = $request->text;

		$specialty = Specialty::findOrFail($id);
		$created = false;

		// Затем добавляем эту специальность в список специальностей учебного заведения
		$employerSpecialty = new EmployerSpecialty();
		$employerSpecialty->specialty()->associate($specialty);
		$employerSpecialty->employer()->associate($employer);
		$employerSpecialty->save();

		session()->put('success', $created ?
			"Специальность \"{$text}\" добавлена" :
			"Список специальностей работодателя изменён"
		);

		return redirect()->route('employer.specialties.index', ['employer' => $employer->getKey()]);
	}

	public function show($id) {
		//
	}


	public function edit($id) {
		//
	}

	public function update(Request $request, $id) {
		//
	}

	public function destroy(Request $request, int $employer, int $specialty) {
		if ($specialty == 0) {
			$id = $request->id;
		} else
			$id = $specialty;

		$specialty = EmployerSpecialty::findOrFail($id);
		$name = $specialty->specialty->getTitle();
		$specialty->delete();

		event(new ToastEvent('success', '', "Специальность &laquo;{$name}&raquo; удалена у работодателя"));
		return true;
	}
}
