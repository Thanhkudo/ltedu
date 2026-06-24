<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInteractionFieldsToQuestionBankItems extends Migration
{
    public function up()
    {
        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->string('interaction_type')->default('normal')->after('answer_mode');
            $table->json('interaction_data')->nullable()->after('interaction_type');
            $table->index('interaction_type');
        });
    }

    public function down()
    {
        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->dropIndex(['interaction_type']);
            $table->dropColumn(['interaction_type', 'interaction_data']);
        });
    }
}