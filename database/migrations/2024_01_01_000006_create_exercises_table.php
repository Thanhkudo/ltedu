<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExercisesTable extends Migration
{
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->comment('Noi dung chi tiet bai tap');
            $table->enum('type', ['reading', 'writing', 'listening', 'speaking', 'grammar', 'vocabulary'])->default('writing');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exercises');
    }
}
