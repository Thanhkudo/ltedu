<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateQuestionGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('question_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('question_categories')->cascadeOnDelete();
            $table->string('type', 20)->default('reading')->index();
            $table->string('title')->nullable();
            $table->longText('passage')->nullable();
            $table->string('audio_url', 2048)->nullable();
            $table->string('difficulty', 20)->default('medium');
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category_id', 'type']);
        });

        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->foreignId('group_id')
                ->nullable()
                ->after('category_id')
                ->constrained('question_groups')
                ->nullOnDelete();
        });

        $existing = DB::table('question_bank_items')
            ->whereIn('context_type', ['reading', 'listening'])
            ->where(function ($query) {
                $query->whereNotNull('passage')->where('passage', '<>', '')
                    ->orWhere(function ($query) {
                        $query->whereNotNull('audio_url')->where('audio_url', '<>', '');
                    });
            })
            ->orderBy('id')
            ->get();

        $groups = [];
        foreach ($existing as $item) {
            $key = implode('|', [
                $item->category_id,
                $item->context_type,
                md5((string) $item->passage),
                md5((string) $item->audio_url),
            ]);

            if (!isset($groups[$key])) {
                $groups[$key] = DB::table('question_groups')->insertGetId([
                    'category_id' => $item->category_id,
                    'type' => $item->context_type,
                    'title' => $item->title ?: ($item->context_type === 'reading' ? 'Bài đọc' : 'Bài nghe'),
                    'passage' => $item->passage,
                    'audio_url' => $item->audio_url,
                    'difficulty' => $item->difficulty ?: 'medium',
                    'is_active' => true,
                    'created_by' => $item->created_by,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('question_bank_items')
                ->where('id', $item->id)
                ->update(['group_id' => $groups[$key]]);
        }
    }

    public function down()
    {
        Schema::table('question_bank_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('group_id');
        });

        Schema::dropIfExists('question_groups');
    }
}
