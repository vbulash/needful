<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Throwable;
use DateTime;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller {
	protected array $checkMap = [];

	public function index(): Factory|View|Application {
		return view('imports.index');
	}

	public function create(Request $request) {}

	protected function verify(Worksheet $sheet, string $file): array {
		$errors = [];
		foreach ($sheet->getRowIterator() as $row) {
			if ($row->getRowIndex() == 1)
				continue; // Пропуск строки заголовка

			foreach ($row->getCellIterator() as $cell) {
				$letter = $cell->getColumn();
				$value = $cell->getValue();
				if (!isset($this->checkMap[$letter]))
					break;

				$template = $this->checkMap[$letter];
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

		return $errors;
	}

	public function errors(Request $request) {
		$title = $request->title;
		$messages = $request->errors;
		return view('imports.errors', compact('title', 'messages'));
	}

	private function sanitize(mixed $value) {
		return $value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText ? $value->getPlainText() : $value;
	}

	protected function getCellValue(Cell $cell, string $type): mixed {
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
