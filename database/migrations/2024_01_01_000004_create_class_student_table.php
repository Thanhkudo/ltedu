<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassStudentTable extends Migration
{
    public function up()
    {
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamp('enrolled_at')->useCurrent();
            $table->enum('status', ['active', 'dropped'])->default('active');
            $table->timestamps();

            // Moi hoc vien chi dang ky 1 lan vao moi lop
            $table->unique(['class_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_student');
    }
}
