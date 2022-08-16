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
        Schema::create('learns', function (Blueprint $table) {
            $table->id();
			$table->datetime('start')->comment('Дата поступления');
			$table->datetime('finish')->nullable()->comment('Дата завершения');
			//
			$table->unsignedBigInteger('student_id')->nullable()->comment('Связанный учащийся');
			$table->foreign('student_id')->references('id')->on('students');
			//
			// Блок учебного заведения
			$table->string('new_school')->nullable()->comment('Новое учебное заведение');
			$table->unsignedBigInteger('school_id')->nullable()->comment('Связанное учебное заведение');
			$table->foreign('school_id')->references('id')->on('schools');
			// Блок специальности
			$table->string('new_specialty')->nullable()->comment('Новая специальность');
			$table->unsignedBigInteger('specialty_id')->nullable()->comment('Связанная специальность');
			$table->foreign('specialty_id')->references('id')->on('specialties');
			//
			$table->tinyInteger('status')->default(0)->comment('Статус активности объекта');
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
        Schema::dropIfExists('learns');
    }
};
