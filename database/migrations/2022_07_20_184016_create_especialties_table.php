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
        Schema::create('especialties', function (Blueprint $table) {
            $table->id();
			//
			$table->unsignedBigInteger('specialty_id')->comment('Специальность у работодателя');
			$table->foreign('specialty_id')->references('id')->on('specialties')->cascadeOnDelete();
			//
			$table->integer('count')->comment('Количество позиций по специальности');
			//
			$table->unsignedBigInteger('internship_id')->comment('Связанная стажировка');
			$table->foreign('internship_id')->references('id')->on('internships')->cascadeOnDelete();
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
        Schema::dropIfExists('especialties');
    }
};
