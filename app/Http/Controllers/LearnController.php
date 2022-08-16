<?php

namespace App\Http\Controllers;

use App\Models\ActiveStatus;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;

class LearnController extends Controller
{
	/**
	 * Process datatables ajax request.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData(Request $request)
	{
		$context = session('context');
		$query = Student::findOrFail($context['student'])->learns()->get();

		/*
		 * 'start',			// Дата поступления
		'finish',			// Дата завершения
		'new_school',		// Новое учебное заведение
		'new_specialty',	// Новая специальность
		'status',			// Статус активности объекта
		 */
		return Datatables::of($query)
			->editColumn('start', fn($learn) => $learn->start->format('d.m.Y'))
			->editColumn('finish', fn($learn) => $learn->finish->format('d.m.Y'))
			->editColumn('birthdate', fn($student) => $student->birthdate->format('d.m.Y'))
			->editColumn('link', fn($student) => $student->user->name)
			->addColumn('action', function ($student) {
				$editRoute = route('students.edit', ['student' => $student->getKey(), 'sid' => session()->getId()]);
				$showRoute = route('students.show', ['student' => $student->getKey(), 'sid' => session()->getId()]);
				$selectRoute = route('students.select', ['student' => $student->id, 'sid' => session()->getId()]);
				$actions = '';

				if (auth()->user()->can('students.edit') || auth()->user()->can('students.edit.' . $student->getKey()))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				if (auth()->user()->can('students.show') || auth()->user()->can('students.show.' . $student->getKey()))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				if (auth()->user()->can('students.destroy') || auth()->user()->can('students.destroy.' . $student->getKey())) {
					$name = $student->getTitle();
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$student->getKey()}, '{$name}')\">\n" .
						"<i class=\"fas fa-trash-alt\"></i>\n" .
						"</a>\n";
				}

				if ($student->status == ActiveStatus::ACTIVE->value)
					$actions .=
						"<a href=\"{$selectRoute}\" class=\"btn btn-primary btn-sm float-left ms-5\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Выбор\">\n" .
						"<i class=\"fas fa-check\"></i>\n" .
						"</a>\n";

				return $actions;
			})
			->make(true);
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
