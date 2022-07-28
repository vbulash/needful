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
        Schema::create('student_specialty', function (Blueprint $table) {
            $table->id();
			//
			$table->unsignedBigInteger('student_id')->comment('Студент');
			$table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
			//
			$table->unsignedBigInteger('specialty_id')->comment('Специальность');
			$table->foreign('specialty_id')->references('id')->on('specialties')->cascadeOnDelete();
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
        Schema::dropIfExists('student_specialty');
    }
};
