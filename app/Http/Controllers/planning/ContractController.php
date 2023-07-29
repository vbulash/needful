<?php

namespace App\Http\Controllers\planning;

use App\Events\ToastEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContractRequest;
use App\Models\Answer;
use App\Models\AnswerStudentStatus;
use App\Models\Contract;
use App\Models\Employer;
use App\Models\Order;
use App\Models\School;
use App\Notifications\NewContract;
use App\Notifications\UpdateContract;
use Illuminate\Http\Request;

class ContractController extends Controller {
	public function create(Request $request, int $order) {
		$_order = Order::findOrFail($order);
		$_start = $_order->start;
		$_finish = $_order->end;
		$_school = $_order->school;
		$_employer = ($request->answer == 0 ?
			Employer::findOrFail($request->employer) :
			(Answer::findOrFail($request->answer))->employer
		);

		return view('planning.contracts.create', [
			'mode' => config('global.create'),
			'order' => $order,
			'employer' => $_employer,
			'school' => $_school,
			'answer' => $request->answer,
			'start' => $_start,
			'finish' => $_finish
		]);
	}

	public function store(StoreContractRequest $request) {
		$contract = new Contract();
		$contract->school()->associate($request->school);

		$_answer = null;
		if ($request->answer == 0)
			$employer = $request->employer;
		else {
			$_answer = Answer::findOrFail($request->answer);
			$employer = $_answer->employer->getKey();
		}
		$contract->employer()->associate($employer);
		$contract->number = $request->number;
		$contract->sealed = $request->sealed;
		// $contract->title = $heap['title'];
		$contract->start = $request->start;
		$contract->finish = $request->finish;
		$contract->scan = Contract::uploadScan($request);
		$contract->save();

		$contract->school->user->allow($contract);
		$contract->employer->user->allow($contract);

		if ($request->answer == 0) {
			$answers = Answer::where('employer_id', $request->employer)->get();
			foreach ($answers as $answer) {
				$answer->contract()->associate($contract);
				$answer->save();
			}
		} else {
			$_answer->contract()->associate($contract);
			$_answer->save();
		}

		foreach ($contract->answers as $answer) foreach ($answer->students as $student)
				if ($student->pivot->status == AnswerStudentStatus::APPROVED->value)
					$student->user->allow($contract);

		$contract->school->user->notify(new NewContract($contract));

		session()->put('success', sprintf("Зарегистрирован договор %s с работодателем &laquo;%s&raquo;",
			$contract->getTitle(), $contract->employer->getTitle()));
		return redirect()->route('planning.answers.index', ['order' => $request->order]);
	}

	public function detach(Request $request) {
		$_answer = Answer::findOrFail($request->answer);
		$_contract = Contract::findOrFail($request->contract);
		$_answer->contract()->dissociate();
		$_answer->save();

		$_contract->school->user->notify(new UpdateContract($_contract));

		return true;
	}

	public function attach(Request $request) {
		$_answer = Answer::findOrFail($request->answer);
		$_contract = Contract::findOrFail($request->contract);
		$_answer->contract()->associate($_contract);
		$_answer->save();

		$_contract->school->user->notify(new UpdateContract($_contract));

		return true;
	}

	public function list(Request $request) {
		$contracts = [];
		Contract::where('school_id', $request->school)
			->where('employer_id', $request->employer)
			->get()
			->each(function ($contract) use (&$contracts) {
				$contracts[] = [
					'id' => $contract->getKey(),
					'text' => $contract->getTitle(),
				];
			});
		$school = School::findOrFail($request->school);
		$employer = Employer::findOrFail($request->employer);
		return response()->json([
			'school' => $school->getTitle(),
			'employer' => $employer->getTitle(),
			'contracts' => $contracts
		]);
	}
}
