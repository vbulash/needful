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
	public function up(): void
	{
		Schema::create('teacher_learn', function (Blueprint $table) {
			$table->id();
			//
			$table->unsignedBigInteger('teacher_id')->comment('Связанный руководитель практики');
			$table->foreign('teacher_id')->references('id')->on('teachers');
			//
			$table->unsignedBigInteger('learn_id')->comment('Связанная запись обучения учащегося');
			$table->foreign('learn_id')->references('id')->on('learns');
			//
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::dropIfExists('teacher_learn');
	}
};
