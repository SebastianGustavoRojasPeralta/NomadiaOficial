<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experiencia_id')->constrained('experiencias')->cascadeOnDelete();
            $table->dateTime('fecha');
            $table->integer('cupos')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('disponibilidades');
    }
};
