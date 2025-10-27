<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // $table->text('description')->nullable()->after('name');
            $table->string('color')->default('#007bff')->after('description'); // For UI
            $table->string('icon')->nullable()->after('color'); // FontAwesome icon
            $table->json('metadata')->nullable()->after('icon');
            $table->integer('item_count')->default(0)->after('metadata');
            // $table->integer('sort_order')->default(0)->after('item_count');
            // $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'color', 'icon', 'metadata', 
                'item_count', 'sort_order', 'is_active'
            ]);
        });
    }
};