<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
	public function getData(int $employer) {
		$query = Employer::findOrFail($employer)->orders;
		// ->wherePivotNotIn('status', [OrderEmployerStatus::REJECTED->value]);
	}

    public function index(int $employer, int $order)
    {
        //
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
