<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->unsignedSmallInteger('duration')->nullable()->comment('Null means use test duration');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('status', ['draft', 'open', 'closed'])->default('draft');
            $table->timestamps();

            $table->index(['class_id', 'status', 'starts_at', 'ends_at']);
            $table->index(['test_id', 'status']);
        });

        Schema::table('test_submissions', function (Blueprint $table) {
            $table->foreignId('test_session_id')->nullable()->after('test_id')->constrained('test_sessions')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('test_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('test_session_id');
        });

        Schema::dropIfExists('test_sessions');
    }
}
