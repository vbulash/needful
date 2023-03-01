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
		Schema::create('answers_students', function (Blueprint $table) {
			$table->id();
			$table->foreignId('answer_id')->constrained()->cascadeOnDelete()->comment('Связанный ответ работодателя');
			$table->foreignId('student_id')->constrained()->cascadeOnDelete()->comment('Связанный учащиийся');
			$table->tinyInteger('status')->default(0)->comment('Статус одобрения учащегося работодателем');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('answers_students');
	}
};