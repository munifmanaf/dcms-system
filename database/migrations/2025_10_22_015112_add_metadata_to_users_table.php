<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('department')->nullable()->after('phone');
            $table->string('position')->nullable()->after('department');
            $table->json('preferences')->nullable()->after('position');
            $table->string('avatar_path')->nullable()->after('preferences');
            $table->timestamp('last_login_at')->nullable()->after('avatar_path');
            $table->string('timezone')->default('UTC')->after('last_login_at');
            $table->json('metadata')->nullable()->after('timezone');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'department', 'position', 'preferences', 
                'avatar_path', 'last_login_at', 'timezone', 'metadata'
            ]);
        });
    }
};