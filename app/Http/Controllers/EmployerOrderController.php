<?php

namespace App\Http\Controllers;

use App\Events\orders\Sent2AnsweredTaskEvent;
use App\Events\ToastEvent;
use App\Models\Employer;
use App\Models\Order;
use App\Models\Answer;
use App\Models\OrderEmployer;
use App\Models\OrderEmployerStatus;
use App\Notifications\orders\Sent2Accept;
use App\Notifications\orders\Sent2Reject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EmployerOrderController extends Controller {
	public function getData(int $employer) {
		$query = Employer::findOrFail($employer)->orders;
		// ->wherePivotNotIn('status', [OrderEmployerStatus::REJECTED->value]);

		return DataTables::of($query)
			->addColumn('school', fn($order) => $order->school->getTitle())
			->addColumn('start', fn($order) => $order->start->format('d.m.Y'))
			->addColumn('end', fn($order) => $order->end->format('d.m.Y'))
			->editColumn('status', fn($order) => OrderEmployerStatus::getName($order->pivot->status))
			->addColumn('action', function ($order) use ($employer) {
				$showRoute = route('employers.orders.show', ['employer' => $employer, 'order' => $order->getKey()]);
				$answerRoute = route('employers.orders.select', ['employer' => $employer, 'order' => $order->getKey()]);
				$items = [];

				$items[] = ['type' => 'item', 'link' => $showRoute, 'icon' => 'fas fa-eye', 'title' => 'Просмотр'];
				if ($order->pivot->status != OrderEmployerStatus::REJECTED->value) {
					$items[] = ['type' => 'item', 'click' => "clickCancel({$employer}, {$order->getKey()}, '{$order->getTitle()}')", 'icon' => 'fas fa-ban', 'title' => 'Отмена'];
				}
				$items[] = ['type' => 'divider'];
				$items[] = ['type' => 'item', 'link' => $answerRoute, 'title' => 'Ответы на заявку'];

				return createDropdown('Действия', $items);
			})
			->make(true);
	}

	public function index(int $employer) {
		$count = Employer::findOrFail($employer)->orders()->count();
		return view('employers.orders.index', compact('employer', 'count'));
	}

	public function select(int $employer, int $order) {
		$order = Order::findOrFail($order);
		$context = session('context');
		$context['order'] = $order->getKey();
		session()->put('context', $context);

		return redirect()->route('employers.orders.answers.index', ['employer' => $employer, 'order' => $order->getKey()]);
	}

	public function show(int $employer, int $order) {
		$mode = config('global.show');
		$order = Order::findOrFail($order);
		return view('employers.orders.show', compact('employer', 'order', 'mode'));
	}

	public function cancel(Request $request) {
		$employer = Employer::findOrFail($request->employer);
		$employer->orders()->updateExistingPivot($request->order, [
			'status' => OrderEmployerStatus::REJECTED->value,
		]);
		$order = Order::findOrFail($request->order);

		event(new ToastEvent('success', '', "Приглашение работодателя на заявку &laquo;{$order->getTitle()}&raquo; отменено"));
		return true;
	}

	public function reject(int $employer, int $order) {
		$_employer = Employer::findOrFail($employer);
		$_employer->orders()->updateExistingPivot($order, [
			'status' => OrderEmployerStatus::REJECTED->value,
		]);
		$query = DB::select(<<<'EOS'
SELECT
    answers.id
FROM
    answers,
    orders_specialties AS os,
    orders,
    employers
WHERE
  employers.id = :employer AND
    orders.id = :order AND
    answers.employer_id = employers.id AND
    answers.orders_specialties_id = os.id AND
    os.order_id = orders.id;
EOS, ['employer' => $employer, 'order' => $order]);
		foreach ($query as $answer) {
			Answer::find($answer->id)->update(['approved' => 0]);
		}
		;
		$_order = Order::findOrFail($order);

		$_order->school->user->notify(new Sent2Reject($_order, $_employer));
		$orderEmployer = OrderEmployer::all()
			->where('order_id', $order)
			->where('employer_id', $employer)
			->first();
		event(new Sent2AnsweredTaskEvent($orderEmployer));
		session()->put('success', "Вы отказались от участия в практике");
		return redirect()->route('employers.orders.answers.index', compact('employer', 'order'));
	}

	public function accept(int $employer, int $order) {
		$_employer = Employer::findOrFail($employer);
		$_employer->orders()->updateExistingPivot($order, [
			'status' => OrderEmployerStatus::ACCEPTED->value,
		]);
		$_order = Order::findOrFail($order);

		$_order->school->user->notify(new Sent2Accept($_order, $_employer));
		$orderEmployer = OrderEmployer::all()
			->where('order_id', $order)
			->where('employer_id', $employer)
			->first();
		event(new Sent2AnsweredTaskEvent($orderEmployer));
		session()->put('success', "Вы согласились принять практикантов");
		return redirect()->route('employers.orders.answers.index', compact('employer', 'order'));
	}
}