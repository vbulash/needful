<?php

namespace App\Http\Controllers\Imports;

use App\Http\Controllers\ImportController;
use App\Notifications\NewLearn;
use Illuminate\Http\Request;
use App\Events\ToastEvent;
use App\Http\Controllers\Auth\RoleName;
use App\Models\ActiveStatus;
use App\Models\Learn;
use App\Models\School;
use App\Models\Specialty;
use App\Models\Student;
use App\Models\User;
use App\Notifications\NewStudent;
use App\Notifications\NewUser;
use App\Notifications\UpdateStudent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Throwable;
use DateTime;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentImportController extends ImportController {
	protected array $checkMap = [
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
		'M' => ['type' => 'text', 'field' => '', 'key' => 'specialty', 'title' => 'Специальность', 'required' => true],
		'N' => ['type' => 'text', 'field' => 'grade', 'title' => 'Класс / группа (на момент заполнения)'],
		'O' => ['type' => 'textarea', 'field' => 'hobby', 'title' => 'Увлечения (хобби)'],
		'P' => ['type' => 'text', 'field' => 'hobbyyears', 'title' => 'Как давно занимается хобби (лет)?'],
		'Q' => ['type' => 'textarea', 'field' => 'contestachievements', 'title' => 'Участие в конкурсах, олимпиадах. Достижения'],
		'R' => ['type' => 'textarea', 'field' => 'dream', 'title' => 'Чем хочется заниматься в жизни?'],
	];

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

		return view('imports.students.create', $params);
	}

	public function download(Request $request) {
		return Storage::download('/assets/template-import-students.xlsx',
			env('APP_NAME') . ' - Шаблон для импорта студентов.xlsx');
	}

	public function downloadSpecialties(Request $request) {
		event(new ToastEvent('info', '', 'Экспорт справочника специальностей...'));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Наименование специальности');
		$sheet->setCellValue('B1', 'Признак специальности');
		$sheet->freezePane('A2');

		$row = 2;
		Specialty::all()
			->sortBy('name')
			->each(function ($specialty) use ($sheet, &$row) {
			    $sheet->setCellValue('A' . $row, $specialty->name);
			    $sheet->setCellValue('B' . $row++, $specialty->federal ? 'Из федерального справочника' : 'Введена вручную');
		    });

		$filename = '/assets/export-specialties.xlsx';
		$writer = new Xlsx($spreadsheet);
		try {
			$writer->save(public_path() . '/uploads' . $filename);
			event(new ToastEvent('success', '', 'Справочник специальностей экспортирован'));
			return Storage::download($filename, env('APP_NAME') . ' - Справочник специальностей.xlsx');
		} catch (Throwable $exc) {
			event(new ToastEvent('error', '', 'Ошибка экспорта справочника специальностей:<br/>' . $exc->getMessage()));
		} finally {
			$spreadsheet->disconnectWorksheets();
			unset($spreadsheet);
		}
	}

	public function upload(Request $request) {
		$school = School::findOrFail($request->school);
		$file = $request->file('upload')->path();
		$rewrite = $request->has('rewrite');

		$spreadsheet = IOFactory::load($file);
		$sheet = $spreadsheet->getActiveSheet();

		// Шаг 1 - проверка данных
		event(new ToastEvent('info', '', 'Шаг 1. Проверка корректности импортируемых данных'));
		$errors = $this->verify($sheet, $file);
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
				if (!isset($this->checkMap[$letter]))
					break;
				$template = $this->checkMap[$letter];
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

			$learn = Learn
				::whereHas('student', function (Builder $query) use ($student) {
				    $query->where('id', $student->getKey());
			    })
				->whereHas('school', function (Builder $query) use ($school) {
				    $query->where('id', $school->getKey());
			    })
				->first();
			if ($learn)
				$learn->delete();

			$learn = new Learn();
			$learn->start = $special['admission'];
			$specialty = Specialty::where('name', $special['specialty'])->first();
			if ($specialty == null) {
				$learn->new_specialty = $special['specialty'];
				$learn->status = ActiveStatus::NEW ->value;
			} else {
				$learn->specialty()->associate($specialty);
				$learn->status = ActiveStatus::ACTIVE->value;
				$learn->new_specialty = null;
			}
			$learn->student()->associate($student);
			$learn->school()->associate($school);
			$learn->save();
			$learn->student->user->allow($school);
			$learn->student->user->notify(new NewLearn($learn));
		}

		//
		session()->put('success', 'Данные учащихся успешно импортированы');
		return redirect()->route('students.index');
	}
}
