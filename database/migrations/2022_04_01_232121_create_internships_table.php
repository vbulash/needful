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
		// Практика
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
			$table->string('iname')->comment('Название практики');
			$table->enum('itype', ['Открытая практика', 'Закрытая практика'])->comment('Тип практики');
			$table->enum('status', ['Планируется', 'Выполняется', 'Закрыта'])->comment('Статус практики');
			$table->text('program')->comment('Программа практики');
			//
			$table->unsignedBigInteger('employer_id')->comment('Связанный работодатель');
			$table->foreign('employer_id')->references('id')->on('employers')->cascadeOnDelete();
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
        Schema::dropIfExists('internships');
    }
};
