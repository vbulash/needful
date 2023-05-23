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
			->addColumn('sealed', fn($contract) => (new DateTime($contract->sealed))->format('d,m.Y'))
			->addColumn('start', fn($contract) => (new DateTime($contract->start))->format('d,m.Y'))
			->addColumn('finish', fn($contract) => (new DateTime($contract->finish))->format('d,m.Y'))
			->addColumn('action', function ($contract) {
				$showRoute = route('contracts.show', [
					'contract' => $contract->id
				]);
				$items = [];
				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];

				if (auth()->user()->hasRole(RoleName::ADMIN->value)) {
					$title = sprintf("%s от %s", $contract->number, (new DateTime($contract->sealed))->format('d.m.Y'));
					$items[] = ['type' => 'item', 'click' => "clickDelete({$contract->id}, '{$title}')", 'icon' => 'fas fa-trash-alt', 'title' => 'Удаление'];
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

	public function show(int $contract) {
		$mode = config('global.show');
		$_contract = Contract::findOrFail($contract);
		return view('contracts.edit', [
			'contract' => $_contract,
			'mode' => $mode
		]);
	}

	public function destroy(Request $request) {
		$contract = Contract::findOrFail($request->id);
		$title = $contract->getTitle();
		$contract->delete();

		event(new ToastEvent('success', '', "Договор на практику № {$title} удалён"));
		return true;
	}
}
