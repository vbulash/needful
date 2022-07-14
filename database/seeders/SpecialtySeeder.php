<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specialties = json_decode(Storage::get('migrations/positions.json'));

		$index = 0;
		foreach ($specialties as $specialty) {
			$item = (array) $specialty;

			$data = [
				'federal' => true,
				'order' => $item["п\\п"],
				'code' => $item['Код'],
				'name' => $item['Наименование профессий рабочих, должностей служащих'],
				'degree' => $item['Квалификация'],
				'level0_id' => null,
				'level1_id' => null,
				'level2_id' => null,
			];
			foreach (['0', '1', '2'] as $level) {
				$field = 'Уровень ' . $level;
				$value = $item[$field];

				if (strlen($value) == 0) continue;

				$dict = Item::where('name', $value)->first();
				if ($dict == null) {
					$dict = Item::create([
						'name' => $value,
					]);
					$dictID = $dict->getKey();
				} else {
					$dictID = $dict->getKey();
				}
				$data['level' . $level . '_id'] = $dictID;
			}

			Specialty::create($data);
			echo $index++ . "\n";
		}
    }
}
