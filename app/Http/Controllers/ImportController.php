<?php

namespace App\Http\Controllers;

use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Models\ActiveStatus;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Notifications\NewStudent;
use App\Notifications\NewUser;
use App\Notifications\UpdateStudent;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;
use DateTime;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class ImportController extends Controller {
	private static array $checkMap = [
		'A' => ['type' => 'text', 'field' => 'lastname', 'title' => 'Фамилия', 'required' => true],
		'B' => ['type' => 'text', 'field' => 'firstname', 'title' => 'Имя', 'required' => true],
		'C' => ['type' => 'text', 'field' => 'surname', 'title' => 'Отчество'],
		'D' => ['type' => 'options', 'field' => 'sex', 'title' => 'Пол', 'required' => true, 'options' => ['Женский', 'Мужской']],
		'E' => ['type' => 'date', 'field' => 'birthdate', 'title' => 'Дата рождения', 'required' => true],
		'F' => ['type' => 'text', 'field' => 'phone', 'title' => 'Телефон', 'required' => true],
		'G' => ['type' => 'email', 'field' => 'email', 'title' => 'Электронная почта', 'required' => true],
		'H' => ['type' => 'textarea', 'field' => 'parents', 'title' => 'ФИО родителей, опекунов (до 14 лет), после 14 лет можно не указывать'],
		'I' => ['type' => 'textarea', 'field' => 'parentscontact', 'title' => 'Контактные телефоны родителей или опекунов'],
		'J' => ['type' => 'textarea', 'field' => 'passport', 'title' => 'Данные документа, удостоверяющего личность (серия, номер, кем и когда выдан)'],
		'K' => ['type' => 'textarea', 'field' => 'address', 'title' => 'Адрес проживания'],
		'L' => ['type' => 'date', 'field' => '', 'key' => 'admission', 'title' => 'Дата поступления в учебное заведение', 'required' => true],
		'M' => ['type' => 'text', 'field' => 'grade', 'title' => 'Класс / группа (на момент заполнения)'],
		'N' => ['type' => 'textarea', 'field' => 'hobby', 'title' => 'Увлечения (хобби)'],
		'O' => ['type' => 'text', 'field' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?'],
		'P' => ['type' => 'textarea', 'field' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения'],
		'Q' => ['type' => 'textarea', 'field' => 'dream', 'title' => 'Чем хочется заниматься в жизни?'],
	];

	public function index(): Factory|View|Application {
		return view('imports.index');
	}

	public function create(Request $request) {
		$params = [];
		$params['schools'] = School::all()
			->sortBy('short')
			->pluck('short', 'id')
			->toArray();

		$school = 0;
		$temp = auth()->user()->schools;
		if ($temp->count() != 0)
			$school = $temp->first()->getKey();
		$params['school'] = $school;

		return view('imports.create', $params);
	}

	public function download(Request $request) {
		return Storage::download('/assets/template-import-students.xlsx',
			env('APP_NAME') . ' - Шаблон для импорта студентов.xlsx');
	}

	public function upload(Request $request) {
		$school = School::findOrFail($request->school);
		$file = $request->file('upload')->path();
		$rewrite = $request->has('rewrite');

		$spreadsheet = IOFactory::load($file);
		$sheet = $spreadsheet->getActiveSheet();

		// Шаг 1 - проверка данных
		event(new ToastEvent('info', '', 'Шаг 1. Проверка корректности импортируемых данных'));
		$errors = [];
		foreach ($sheet->getRowIterator() as $row) {
			if ($row->getRowIndex() == 1)
				continue; // Пропуск строки заголовка

			foreach ($row->getCellIterator() as $cell) {
				$letter = $cell->getColumn();
				$value = $cell->getValue();
				if (!isset(self::$checkMap[$letter]))
					break;

				$template = self::$checkMap[$letter];
				$place = sprintf("Ячейка %s%s (столбец &laquo;%s&raquo;) :", $letter, $row->getRowIndex(), $template['title']);
				if (isset($template['required']) && !isset($value))
					$errors[] = "$place Нет обязательных данных в ячейке";

				if (isset($value)) {
					$value = $this->sanitize($value);
					switch ($template['type']) {
						case 'int':
							if (!filter_var($value, FILTER_VALIDATE_INT))
								$errors[] = "$place В ячейке ожидается целое число";
							break;
						case 'text':
							if (strlen($value) > 255)
								$errors[] = "$place Размер значения в ячейке превышает допустимый (255 символов)";
							break;
						case 'textarea':
							if (strlen($value) > 65535)
								$errors[] = "$place Размер значения в ячейке превышает допустимый (65535 символов)";
							break;
						case 'email':
							if (!filter_var($value, FILTER_VALIDATE_EMAIL))
								$errors[] = "$place В ячейке ожидается корректный адрес электронной почты";
							if (strlen($value) > 255)
								$errors[] = "$place Размер значения в ячейке первышает допустимый (255 символов)";
							break;
						case 'date':
							try {
								$temp = match (strval(gettype($value))) {
									'integer' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value),
									'string' => new DateTime($value),
									default => null
								};
							} catch (Throwable $exc) {
								$temp = null;
							}
							if ($temp == null)
								$errors[] = "$place В ячейке ожидается корректная дата";
							break;
						case 'options':
							if (!in_array($value, $template['options']))
								$errors[] = "$place В ячейке допускаются только следующие значения - " .
									Arr::join($template['options'], ' или ');
							break;
					}
				}
			}
		}
		if ($row->getRowIndex() <= 1)
			$errors[] = 'Таблица пуста - нет данных';

		if (count($errors) > 0)
			return redirect()->route('import.errors', [
				'title' => 'Ошибки проверки данных таблицы для импорта учащихся',
				'errors' => $errors
			]);

		// Шаг 2. Импорт данных
		event(new ToastEvent('info', '', 'Шаг 2. Импорт таблицы данных учащихся'));
		foreach ($sheet->getRowIterator() as $row) {
			if ($row->getRowIndex() == 1)
				continue; // Пропуск строки заголовка

			$data = [];
			$special = [];
			foreach ($row->getCellIterator() as $cell) {
				$letter = $cell->getColumn();
				if (!isset(self::$checkMap[$letter]))
					break;
				$template = self::$checkMap[$letter];
				if (isset($template['key']))
					$special[$template['key']] = $this->getCellValue($cell, $template['type']);
				else
					$data[$template['field']] = $this->getCellValue($cell, $template['type']);
			}
			$user = User::where('email', $data['email'])->first();
			if ($user == null) { // Создать нового пользователя
				$user = User::create([
					'name' => Str::of(sprintf("%s %s %s", $data['lastname'], $data['firstname'], $data['surname'] ?? ''))->trim(),
					'email' => $data['email'],
					'password' => Hash::make(Str::random(20)),
				]);
				$user->assignRole(RoleName::TRAINEE->value);
				event(new Registered($user));
				$user->notify(new NewUser());
			}
			$student = Student::where('email', $data['email'])->first();
			if ($student == null) { // Создать карточку нового учащегося
				$student = new Student();
				$student->fill($data);
				$student->user()->associate($user);
				$student->status = ActiveStatus::NEW ->value;
				$student->save();

				$student->user->allow($student);
				$student->user->notify(new NewStudent($student));
			}
			if ($rewrite) { // Перезаписываем карточку существующего участника
				$student->fill($data);
				$student->user()->associate($user);
				$student->status = ActiveStatus::NEW ->value;
				$student->update();

				$student->user->allow($student);
				$student->user->notify(new UpdateStudent($student));
			} // Иначе просто пропускаем
		}

		//
		session()->put('success', 'Данные учащихся успешно импортированы');
		return redirect()->route('students.index');
	}

	public function errors(Request $request) {
		$title = $request->title;
		$messages = $request->errors;
		return view('imports.errors', compact('title', 'messages'));
	}

	private function sanitize(mixed $value) {
		return $value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText ? $value->getPlainText() : $value;
	}

	private function getCellValue(Cell $cell, string $type): mixed {
		$letter = $cell->getColumn();
		$value = $this->sanitize($cell->getValue());

		if ($type == 'date')
			return match (strval(gettype($value))) {
				'integer' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value),
				'string' => new DateTime($value),
				default => null
			};
		return $value;
	}
}
