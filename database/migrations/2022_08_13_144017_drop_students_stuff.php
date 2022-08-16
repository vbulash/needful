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
		Schema::table('students', function (Blueprint $table) {
			$table->dropColumn('institutions');
			$table->dropColumn('position');
		});
		Schema::dropIfExists('student_school');
		Schema::dropIfExists('student_specialty');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('schools', function (Blueprint $table) {
			$table->string('institutions')->comment('Учебное заведение (на момент заполнения)')->nullable();
			$table->string('position')->nullable()->comment('Специальность студента (введена вручную)');
		});
		Schema::create('student_school', function (Blueprint $table) {
			$table->id();
			//
			$table->unsignedBigInteger('student_id')->comment('Студент');
			$table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
			//
			$table->unsignedBigInteger('school_id')->comment('Учебное заведение');
			$table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
			//
			$table->timestamps();
		});
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
};
