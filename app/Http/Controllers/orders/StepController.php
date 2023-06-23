<?php

namespace App\Http\Controllers\orders;

use App\Events\orders\New2SentTaskEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WizardButtons;
use App\Models\Answer;
use App\Models\Order;
use App\Models\OrderEmployer;
use App\Models\OrderEmployerStatus;
use App\Models\OrderSpecialty;
use App\Models\School;
use App\Notifications\NewOrder;
use App\Notifications\orders\New2Sent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StepController extends Controller {
	public static array $steps = [
		StepSchool::class,
		StepOrder::class,
		StepSpecialties::class,
		StepEmployers::class,
		StepFinal::class,
	];

	public function getData(Request $request) {
		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();
		return $step->isBrowse() ? $step->getBrowseData($request) : null;
	}

	/**
	 * Проигрывание хода мастера заявки на практику
	 */
	public function play(Request $request) {
		if (!$request->has('continue'))
			self::clearCurrentStep();

		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();
		$buttons = 0;
		if (self::getCurrentStep() != 0)
			$buttons |= WizardButtons::BACK->value;
		if (self::getCurrentStep() == count(self::$steps) - 1) {
			if (!$step->isBrowse())
				$buttons |= WizardButtons::FINISH->value;
		} elseif (!$step->isBrowse())
			$buttons |= WizardButtons::NEXT->value;
		$request->merge([
			'buttons' => $buttons
		]);

		return $step->run($request);
	}

	/**
	 * Шаг назад
	 */
	public function back(Request $request): RedirectResponse {
		if (self::getCurrentStep() > 0)
			self::decrementCurrentStep();

		return redirect()->route('orders.steps.play', ['continue' => true]);
	}

	/**
	 * Шаг вперед
	 */
	public function next(Request $request): RedirectResponse {
		if (self::getCurrentStep() == count(self::$steps) - 1)
			return redirect()->route('orders.steps.finish', $request->all());

		// При шаге вперед нужно сохранить результаты текущего шага
		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();

		Validator::make(
			data: $request->all(),
			rules: $step->getStoreRules(),
			customAttributes: $step->getStoreAttributes()
		)->validate();

		if (!$step->store($request))
			return redirect()->route('orders.steps.play', ['continue' => true]);

		if (self::getCurrentStep() < count(self::$steps) - 1)
			self::incrementCurrentStep();

		return redirect()->route('orders.steps.play', ['continue' => true]);
	}

	/**
	 * Финальное сохранение заявки на практику
	 */
	public function finish(Request $request) {
		// Сохранить информацию последнего шага
		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();

		Validator::make(
			data: $request->all(),
			rules: $step->getStoreRules(),
			customAttributes: $step->getStoreAttributes()
		)->validate();

		$result = $step->store($request);

		self::clearCurrentStep();

		if (!$result)
			return null;

		// Полное сохранение
		$heap = session('heap');
		$order = new Order();
		$order->school()->associate($heap['school']);
		$order->name = $heap['name'];
		$order->start = $heap['start'];
		$order->end = $heap['end'];
		$order->place = $heap['place'];
		$order->description = $heap['description'];
		$order->save();

		$school = School::findOrFail($heap['school']);
		$school->user->allow($order);

		foreach ($heap['specialties'] as $item) {
			$orderSpecialty = new OrderSpecialty();
			$orderSpecialty->quantity = $item->quantity;
			$orderSpecialty->order()->associate($order);
			$orderSpecialty->specialty()->associate($item->id);
			$orderSpecialty->save();
		}
		foreach ($heap['employers'] as $item) {
			$orderEmployer = new OrderEmployer();
			$orderEmployer->status = OrderEmployerStatus::NEW ->value;
			$orderEmployer->order()->associate($order);
			$orderEmployer->employer()->associate($item->id);
			$orderEmployer->save();

			$orderEmployer->employer->user->allow($order);

			foreach ($orderEmployer->order->specialties as $orderSpecialty) {
				// foreach (OrderSpecialty::all() as $orderSpecialty) {
				$answer = new Answer();
				$answer->approved = 0;
				$answer->orderSpecialty()->associate($orderSpecialty);
				$answer->employer()->associate($item->id);
				$answer->save();
			}
		}

		$order->school->user->notify(new NewOrder($order));
		foreach ($order->employers as $order_employer) {
			$order_employer->employer->user->notify(new New2Sent($order));
			$order_employer->update([
				'status' => OrderEmployerStatus::SENT->value,
			]);
			event(new New2SentTaskEvent($order_employer));
		}

		session()->forget('heap');
		session()->put('success', "Заявка на практику \"{$order->name}\" создана");

		return redirect()->route('orders.show', ['order' => $order->getKey()]);
	}

	public function close(Request $request) {
		self::clearCurrentStep();
		session()->forget('heap');
		return redirect()->route('dashboard');
	}

	/**
	 * @return int
	 */
	public static function getCurrentStep(): int {
		$currentStep = session('step');
		if (!isset($currentStep)) {
			$currentStep = 0;
			session()->put('step', $currentStep);
		}
		return $currentStep;
	}

	/**
	 * @param int $currentStep
	 */
	public static function setCurrentStep(int $currentStep): void {
		session()->put('step', $currentStep);
	}

	public static function incrementCurrentStep(): int {
		return session()->increment('step');
	}

	public static function decrementCurrentStep(): int {
		return session()->decrement('step');
	}

	public static function clearCurrentStep(): void {
		session()->forget('step');
	}

	protected static function getCurrentStepClass(): string {
		return self::$steps[self::getCurrentStep()];
	}

	public function isLastStep(): bool {
		return self::getCurrentStep() == count(self::$steps) - 1;
	}
}
