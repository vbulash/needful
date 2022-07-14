<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Специальность
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('items', function (Blueprint $table) {
			$table->id();
			$table->text('name');
			$table->timestamps();
		});

        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
			$table->boolean('federal')->default(false)->comment('Признак федерального справочника профессий');
			$table->string('order', 10)->nullable()->comment('Номер по порядку в федеральном справочнике');
			$table->string('code', 10)->nullable()->comment('Код из федерального справочника');
			$table->string('name')->comment('Название специальности');
			$table->string('degree', 24)->nullable()->comment('Квалификация по федеральному справочнику');
			//
			$table->unsignedBigInteger('level0_id')->nullable()->comment('Уровень 0');
			$table->foreign('level0_id')->references('id')->on('items')->cascadeOnDelete();
			//
			$table->unsignedBigInteger('level1_id')->nullable()->comment('Уровень 1');
			$table->foreign('level1_id')->references('id')->on('items')->cascadeOnDelete();
			//
			$table->unsignedBigInteger('level2_id')->nullable()->comment('Уровень 2');
			$table->foreign('level2_id')->references('id')->on('items')->cascadeOnDelete();
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

		Schema::dropIfExists('specialties');
		Schema::dropIfExists('items');
	}
};
