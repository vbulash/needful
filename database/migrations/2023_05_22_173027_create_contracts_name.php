<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('contracts', function (Blueprint $table) {
			$table->id();
			$table->string('number')->comment('Номер договора');
			$table->date('sealed')->comment('Дата подписания договора');
			$table->foreignId('school_id')->constrained()->cascadeOnDelete()->comment('Связанное образовательное учреждение');
			$table->foreignId('employer_id')->constrained()->cascadeOnDelete()->comment('Связанный работодатель');
			$table->string('title')->nullable()->comment('Название контракта практики');
			$table->date('start')->comment('Дата начала практики');
			$table->date('finish')->comment('Дата завершения практики');
			$table->string('scan')->nullable()->comment('Скан бумажного договора');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('contracts');
	}
};