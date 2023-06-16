<?php

namespace App\Http\Controllers;

use App\Models\AnswerStatus;
use App\Models\Contract;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContractStudentController extends Controller
{
	protected function getStudents(Contract $contract): iterable {
		$data = [];
		foreach ($contract->answers as $answer) {
			if ($answer->status != AnswerStatus::DONE->value)
				continue;
			$specialty = $answer->orderSpecialty->specialty->name;
			foreach ($answer->students as $student) {
				$data[] = [
					'specialty' => $specialty,
					'student' => $student->getTitle(),
					'phone' => $student->phone,
					'email' => $student->email
				];
			}
		}
		return $data;
	}

    public function index(int $contract) {
		$_contract = Contract::findOrFail($contract);
		$data = $this->getStudents($_contract);

		return view('contracts.students.index', [
			'count' => count($data),
			'contract' => $_contract
		]);
	}

	public function getData(int $contract) {
		$_contract = Contract::findOrFail($contract);
		$data = $this->getStudents($_contract);

		return DataTables::of($data)
			->addColumn('action', fn ($item) => '')
			->make(true);
	}
}
