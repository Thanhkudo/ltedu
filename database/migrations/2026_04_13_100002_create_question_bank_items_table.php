<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionBankItemsTable extends Migration
{
    public function up()
    {
        Schema::create('question_bank_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('question_categories')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('question_text');
            $table->longText('passage')->nullable()->comment('Doan van cho dang reading');
            $table->string('audio_url')->nullable()->comment('File audio cho dang listening');
            $table->enum('answer_mode', ['select', 'input'])->default('select');
            $table->enum('context_type', ['normal', 'reading', 'listening'])->default('normal');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('correct_answer')->nullable()->comment('Cho dang input');
            $table->text('explanation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category_id', 'answer_mode', 'context_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_bank_items');
    }
}
