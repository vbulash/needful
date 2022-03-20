<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use \Exception;

class EmployerController extends Controller
{
	/*
	 * CRUD::addColumn(['name' => 'inn', 'label' => 'ИНН']);
		CRUD::addColumn(['name' => 'name', 'label' => 'Наименование']);
		CRUD::addColumn(['name' => 'address', 'label' => 'Фактический адрес']);
		CRUD::addColumn(['name' => 'phone', 'label' => 'Телефон']);
		CRUD::addColumn(['name' => 'email', 'label' => 'Электронная почта']);
	 */
	/**
	 * Process datatables ajax request.
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function getData()
	{
		return Datatables::of(Employer::all())
			->addColumn('action', function ($employer) {
				$editRoute = route('employers.edit', ['employer' => $employer->id, 'sid' => session()->getId()]);
				$showRoute = route('employers.show', ['employer' => $employer->id, 'sid' => session()->getId()]);
				$actions = '';

				if (Auth::user()->can('employers.edit'))
					$actions .=
						"<a href=\"{$editRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Редактирование\">\n" .
						"<i class=\"fas fa-edit\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('employers.show'))
					$actions .=
						"<a href=\"{$showRoute}\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Просмотр\">\n" .
						"<i class=\"fas fa-eye\"></i>\n" .
						"</a>\n";
				if (Auth::user()->can('employers.destroy'))
					$actions .=
						"<a href=\"javascript:void(0)\" class=\"btn btn-primary btn-sm float-left mr-1\" " .
						"data-toggle=\"tooltip\" data-placement=\"top\" title=\"Удаление\" onclick=\"clickDelete({$employer->name})\">\n" .
						"<i class=\"fas fa-trash-alt\"></i>\n" .
						"</a>\n";
				return $actions;
			})
			->make(true);
	}

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
	 */
    public function index()
    {
        $count = Employer::all()->count();
		return view('employers.index', compact('count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
