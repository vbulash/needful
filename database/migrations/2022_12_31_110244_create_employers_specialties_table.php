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
        Schema::create('employers_specialties', function (Blueprint $table) {
            $table->id();
			$table->foreignId('employer_id')->constrained()->cascadeOnDelete()->comment('Связанный работодатель');
			$table->foreignId('specialty_id')->constrained()->cascadeOnDelete()->comment('Связанная специальность');
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
        Schema::dropIfExists('employers_specialties');
    }
};
