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
            $table->longText('content')->comment('Nội dung bài làm của học viên');
            $table->string('file_path')->nullable()->comment('Đường dẫn file đính kèm');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable()->comment('Nhận xét của giáo viên');
            $table->enum('status', ['pending', 'submitted', 'graded'])->default('submitted');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            // Unique: mỗi học viên chỉ nộp 1 bài/1 assignment
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignment_submissions');
    }
}
