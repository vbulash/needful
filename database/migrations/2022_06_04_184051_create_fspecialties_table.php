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
        Schema::create('fspecialties', function (Blueprint $table) {
            $table->id();
			//
			$table->unsignedBigInteger('specialty_id')->comment('Специальность в учебном заведении');
			$table->foreign('specialty_id')->references('id')->on('specialties')->cascadeOnDelete();
			//
			$table->unsignedBigInteger('school_id')->comment('Связанное учебное заведение');
			$table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
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
        Schema::dropIfExists('fspecialties');
    }
};
