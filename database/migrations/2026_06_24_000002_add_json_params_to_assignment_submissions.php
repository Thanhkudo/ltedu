<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJsonParamsToAssignmentSubmissions extends Migration
{
    public function up()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->json('json_params')->nullable()->after('content');
        });
    }

    public function down()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropColumn('json_params');
        });
    }
}
