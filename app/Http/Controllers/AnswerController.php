<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller {
	public function getData(int $employer, int $order) {
		$query = DB::select(<<<SQL
SELECT
    a.id,
	s.name,
	os.quantity,
	a.approved
FROM
    answers AS a,
    orders_specialties AS os,
    specialties AS s
WHERE
    a.orders_specialties_id = os.id
        AND os.specialty_id = s.id
		AND os.order_id = :order
		AND a.employer_id = :employer
SQL,
		[
			'order' => $order,
			'employer' => $employer
		]);
	}

	public function index() {
		//
	}
	public function create() {
		//
	}

	public function store(Request $request) {
		//
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

	public function destroy($id) {
		//
	}
}
