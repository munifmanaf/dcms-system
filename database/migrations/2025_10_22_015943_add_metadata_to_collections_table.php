<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
            // $table->text('description')->nullable()->after('name');
            // $table->foreignId('community_id')->nullable()->after('description')->constrained()->onDelete('cascade');
            $table->string('banner_path')->nullable()->after('community_id');
            $table->json('filters')->nullable()->after('banner_path'); // For smart collections
            $table->json('metadata')->nullable()->after('filters');
            $table->boolean('is_smart')->default(false)->after('metadata'); // Smart collection with filters
            $table->integer('item_count')->default(0)->after('is_smart');
            // $table->integer('sort_order')->default(0)->after('item_count');
        });
    }

    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'community_id', 'banner_path', 'filters',
                'metadata', 'is_smart', 'item_count', 'sort_order'
            ]);
        });
    }
};