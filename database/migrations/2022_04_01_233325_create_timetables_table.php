<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// Графики практики
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
			$table->date('start')->comment('Начало');
			$table->date('end')->comment('Завершение');
			$table->string('name')->comment('Наименование или тема графика практики')->nullable();
			//
			$table->unsignedBigInteger('internship_id')->comment('Связанная практика');
			$table->foreign('internship_id')->references('id')->on('internships')->cascadeOnDelete();
			//
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetables');
    }
};
