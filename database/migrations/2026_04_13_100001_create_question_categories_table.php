<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('question_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('grade_level')->comment('6-9');
            $table->enum('skill_type', ['listening', 'speaking', 'reading', 'writing', 'grammar', 'vocabulary']);
            $table->string('topic')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['grade_level', 'skill_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_categories');
    }
}
