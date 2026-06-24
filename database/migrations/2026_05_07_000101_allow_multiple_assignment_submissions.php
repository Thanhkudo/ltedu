<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowMultipleAssignmentSubmissions extends Migration
{
    public function up()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->index(['assignment_id', 'student_id'], 'assignment_submissions_assignment_student_idx');
            $table->dropUnique(['assignment_id', 'student_id']);
            $table->index(['assignment_id', 'student_id', 'submitted_at'], 'assignment_submissions_assignment_student_submitted_idx');
        });
    }

    public function down()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropIndex('assignment_submissions_assignment_student_submitted_idx');
            $table->dropIndex('assignment_submissions_assignment_student_idx');
            $table->unique(['assignment_id', 'student_id']);
        });
    }
}
