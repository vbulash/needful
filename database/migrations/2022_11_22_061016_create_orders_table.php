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
		// Заявка на практику со стороны учебного заведения
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
			$table->date('start')->comment('Начало практики');
			$table->date('end')->comment('Завершение практики');
			$table->string('name')->comment('Наименование практики');
			$table->foreignId('school_id')->constrained()->cascadeOnDelete()->comment('Связанное учебное заведение');
            $table->timestamps();
        });

		// Специальности в заявке
		Schema::create('orders_specialties', function (Blueprint $table) {
			$table->id();
			$table->foreignId('order_id')->constrained()->cascadeOnDelete()->comment('Связанная заявка на практику');
			//
			$table->unsignedBigInteger('specialty_id');
			$table->foreign('specialty_id')->references('id')->on('specialties')->cascadeOnDelete()->comment('Связанная специальность заявки');
			$table->tinyInteger('quantity')->comment('Количество позиций по специальности');
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
		Schema::dropIfExists('orders_specialties');
		Schema::dropIfExists('orders');
    }
};
