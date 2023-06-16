<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Models\Contract;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ContractController extends Controller {
	public function getData() {
		$query = DB::select(<<<EOS
SELECT
	contracts.id,
	schools.short as school,
	employers.short as employer,
	contracts.`number`,
	contracts.sealed,
	contracts.`start`,
	contracts.finish
FROM
	contracts,
	schools,
	employers
WHERE
	contracts.school_id = schools.id
	AND contracts.employer_id = employers.id
EOS);
		return DataTables::of($query)
			->addColumn('type', function ($contract) {
				$_contract = Contract::findOrFail($contract->id);
				return $_contract->answers()->count() == 0 ? 'Рамочный' : 'Реальный';
			})
			->addColumn('sealed', fn($contract) => (new DateTime($contract->sealed))->format('d,m.Y'))
			->addColumn('start', fn($contract) => (new DateTime($contract->start))->format('d,m.Y'))
			->addColumn('finish', fn($contract) => (new DateTime($contract->finish))->format('d,m.Y'))
			->addColumn('action', function ($contract) {
				$editRoute = route('contracts.edit', [
					'contract' => $contract->id
				]);
				$showRoute = route('contracts.show', [
					'contract' => $contract->id
				]);
				$selectRoute = route('contracts.select', ['contract' => $contract->id]);
				$items = [];

				$items[] = ['type' => 'item', 'link' => $editRoute, 'icon' => 'fas fa-edit', 'title' => 'Редактирование'];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];

				if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
					$title = sprintf("%s от %s", $contract->number, (new DateTime($contract->sealed))->format('d.m.Y'));
					$items[] = ['type' => 'item', 'click' => "clickDelete({$contract->id}, '{$title}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];
				}

				$_contract = Contract::findOrFail($contract->id);
				if ($_contract->answers()->count() != 0) {
					$items[] = ['type' => 'divider'];
					$items[] = ['type' => 'item', 'link' => $selectRoute, 'icon' => 'fas fa-check', 'title' => 'Практиканты'];
				}

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index() {
		session()->forget('context');
		$count = Contract::all()->count();

		return view('contracts.index', compact('count'));
	}

	public function select(int $contract) {
		session()->put('context', ['contract' => $contract]);

		return redirect()->route('contracts.students.index', compact('contract'));
	}

	public function show(int $contract) {
		return $this->edit($contract, true);
	}

	public function edit(int $contract, bool $show = false) {
		$mode = $show ? config('global.show') : config('global.edit');
		$_contract = Contract::findOrFail($contract);
		return view('contracts.edit', [
			'contract' => $_contract,
			'mode' => $mode
		]);
	}

	public function update(Request $request, int $contract) {
		$_contract = Contract::findOrFail($contract);
		$_contract->number = $request->number;
		$_contract->sealed = new DateTime($request->sealed);
		$_contract->start = new DateTime($request->start);
		$_contract->finish = new DateTime($request->finish);
		if ($request->has('clearscan')) {
			$_contract->scan = null;
		} else {
			$scanPath = Contract::uploadScan($request, $_contract->scan);
			if (isset($scanPath))
				$_contract->scan = $scanPath;
		}
		$_contract->update();

		session()->put('success', "Договор {$_contract->getTitle()} изменён");
		return redirect()->route('contracts.index');
	}

	public function destroy(Request $request) {
		$contract = Contract::findOrFail($request->id);
		$title = $contract->getTitle();
		$contract->delete();

		event(new ToastEvent('success', '', "Договор на практику № {$title} удалён"));
		return true;
	}
}
