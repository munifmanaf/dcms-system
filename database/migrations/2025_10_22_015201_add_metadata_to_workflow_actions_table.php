<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('workflow_actions', function (Blueprint $table) {
            $table->json('action_metadata')->nullable()->after('metadata');
            $table->integer('processing_time')->nullable()->after('action_metadata'); // in seconds
            $table->string('ip_address')->nullable()->after('processing_time');
            $table->string('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down()
    {
        Schema::table('workflow_actions', function (Blueprint $table) {
            $table->dropColumn(['action_metadata', 'processing_time', 'ip_address', 'user_agent']);
        });
    }
};
