<?php

namespace App\Http\Controllers\contracts;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WizardButtons;
use App\Models\Contract;
use App\Notifications\NewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StepController extends Controller {
	public static array $steps = [
		StepSchool::class,
		StepEmployer::class,
		StepContract::class,
		StepFinal::class,
	];

	public function getData(Request $request) {
		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();
		return $step->isBrowse() ? $step->getBrowseData($request) : null;
	}

	/**
	 * Проигрывание хода мастера договора на практику
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

		return redirect()->route('contracts.steps.play', ['continue' => true]);
	}

	/**
	 * Шаг вперед
	 */
	public function next(Request $request): RedirectResponse {
		if (self::getCurrentStep() == count(self::$steps) - 1)
			return redirect()->route('contracts.steps.finish', $request->all());

		// При шаге вперед нужно сохранить результаты текущего шага
		$stepClass = $this->getCurrentStepClass();
		$step = new $stepClass();

		Validator::make(
			data: $request->all(),
			rules: $step->getStoreRules(),
			customAttributes: $step->getStoreAttributes()
		)->validate();

		if (!$step->store($request))
			return redirect()->route('contracts.steps.play', ['continue' => true]);

		if (self::getCurrentStep() < count(self::$steps) - 1)
			self::incrementCurrentStep();

		return redirect()->route('contracts.steps.play', ['continue' => true]);
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
		$contract = new Contract();
		$contract->school()->associate($heap['school']);
		$contract->employer()->associate($heap['employer']);
		$contract->number = $heap['number'];
		$contract->sealed = $heap['sealed'];
		// $contract->title = $heap['title'];
		$contract->start = $heap['start'];
		$contract->finish = $heap['finish'];
		// TODO Реализовать scan
		$contract->save();

		$contract->school->user->allow($contract);
		$contract->employer->user->allow($contract);

		$contract->school->user->notify(new NewContract($contract));

		session()->forget('heap');
		session()->put('success', "Договор на практику № {$contract->number} от {$contract->sealed->format('d.m.Y')} зарегистрирован");

		// return redirect()->route('dashboard');
		return redirect()->route('contracts.show', ['contract' => $contract->getKey()]);
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
