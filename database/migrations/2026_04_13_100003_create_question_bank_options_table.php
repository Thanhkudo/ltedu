<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionBankOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('question_bank_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_item_id')->constrained('question_bank_items')->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedSmallInteger('order_index')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_bank_options');
    }
}
