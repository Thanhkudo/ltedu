<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenerationFieldsToAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('generation_mode')->default('manual')->after('exercise_id');
            $table->json('generation_config')->nullable()->after('generation_mode');
            $table->unsignedSmallInteger('generated_question_count')->nullable()->after('generation_config');
        });
    }

    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['generation_mode', 'generation_config', 'generated_question_count']);
        });
    }
}
