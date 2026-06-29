<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->longText('content')->comment('Noi dung bai lam cua hoc vien');
            $table->string('file_path')->nullable()->comment('Duong dan file dinh kem');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable()->comment('Nhan xet cua giao vien');
            $table->enum('status', ['pending', 'submitted', 'graded'])->default('submitted');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            // Unique: moi hoc vien chi nop 1 bai/1 assignment
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignment_submissions');
    }
}
