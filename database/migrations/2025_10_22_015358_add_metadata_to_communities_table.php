<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('communities', function (Blueprint $table) {
            // $table->text('description')->nullable()->after('name');
            $table->string('logo_path')->nullable()->after('description');
            $table->string('banner_path')->nullable()->after('logo_path');
            $table->json('settings')->nullable()->after('banner_path');
            $table->json('metadata')->nullable()->after('settings');
            // $table->boolean('is_public')->default(true)->after('metadata');
            $table->integer('item_count')->default(0)->after('is_public');
        });
    }

    public function down()
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'logo_path', 'banner_path', 'settings',
                'metadata', 'is_public', 'item_count'
            ]);
        });
    }
};