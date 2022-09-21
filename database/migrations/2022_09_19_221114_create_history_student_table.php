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
        Schema::create('history_student', function (Blueprint $table) {
            $table->id();
			//
			$table->unsignedBigInteger('history_id')->nullable()->comment('Связанная история практик');
			$table->foreign('history_id')->references('id')->on('histories')->cascadeOnDelete();
			//
			$table->unsignedBigInteger('student_id')->nullable()->comment('Связанный практикант');
			$table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
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
        Schema::dropIfExists('history_student');
    }
};
