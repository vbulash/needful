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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
			$table->foreignId('employer_id')->constrained()->cascadeOnDelete()->comment('Связанный работодатель');
			$table->unsignedBigInteger('orders_specialties_id')->nullable()->comment('Связанная специальность в заявке');
			$table->foreign('orders_specialties_id')->references('id')->on('orders_specialties')->cascadeOnDelete();
			$table->integer('approved')->comment('Одобренное количество практикантов');
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
        Schema::dropIfExists('answers');
    }
};
