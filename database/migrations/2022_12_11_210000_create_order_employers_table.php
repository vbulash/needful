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
        Schema::create('orders_employers', function (Blueprint $table) {
            $table->id();
			$table->foreignId('order_id')->constrained()->cascadeOnDelete()->comment('Связанная заявка на практику');
			$table->foreignId('employer_id')->constrained()->cascadeOnDelete()->comment('Связанный работодатель');
			$table->tinyInteger('status')->comment('Статус взаимодействия с работодателем');
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
        Schema::dropIfExists('orders_employers');
    }
};
