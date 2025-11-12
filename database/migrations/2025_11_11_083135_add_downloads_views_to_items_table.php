<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // migration file
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // $table->integer('download_count')->default(0);
            // $table->integer('view_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            $table->timestamp('last_viewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
};
