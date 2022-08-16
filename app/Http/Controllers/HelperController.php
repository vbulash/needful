<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\School;
use App\Models\Student;
use App\Notifications\NewSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HelperController extends Controller
{
	public function generatePassword(int $length): array
	{
		return [
			'password' => Str::random($length)
		];
	}

	public function uploadCKEditor(Request $request)
	{
		if ($request->has('upload')) {
			$folder = date('Y-m-d');
			$file = $request->file('upload')->store("images/{$folder}");
			return response()->json([
				'url' => '/uploads/' . $file
			]);
		} else return response()->json([
			'error' => ['message' => 'Внутренняя ошибка текстового редактора: потерян файл для загрузки на сервер']
		]);
	}

	public function support(Request $request) {
		$message = $_POST['message'];
		$user = auth()->user();
		$user->notify(new NewSupport($message));
		return response(status: 200);

		// Ошибка отправляется так,
		//return response()->json(['message' => 'Письмо администратору платформы отправлено'], 404);
	}

	public function contextAddress(): ?string
	{
		$context = session('context');
		if (!isset($context)) return null;

		foreach ($context as $key => $value) {
			$object = match ($key) {
				'employer' => Employer::findOrFail($value),
				'school' => School::findOrFail($value),
				'student' => Student::findOrFail($value),
				default => null
			};
			if (isset($object)) return $object->user->email;
		}
		return null;
	}
}
