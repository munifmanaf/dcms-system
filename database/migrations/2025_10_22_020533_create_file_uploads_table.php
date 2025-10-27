<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level'); // info, warning, error, critical
            $table->string('module'); // auth, workflow, upload, system
            $table->text('message');
            $table->json('context')->nullable(); // Additional context data
            $table->string('user_id')->nullable(); // Who performed the action
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['level', 'module', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
};