// database/migrations/xxxx_xx_xx_add_thumbnail_id_to_items_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // Add thumbnail reference
            $table->foreignId('thumbnail_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('images')
                  ->nullOnDelete();
                  
            // Add imageable columns to images table
            Schema::table('images', function (Blueprint $table) {
                $table->nullableMorphs('imageable');
                $table->boolean('is_featured')->default(false);
                $table->string('alt_text')->nullable();
                $table->text('caption')->nullable();
                $table->integer('order')->default(0);
            });
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['thumbnail_id']);
            $table->dropColumn('thumbnail_id');
        });
        
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn(['imageable_id', 'imageable_type', 'is_featured', 'alt_text', 'caption', 'order']);
        });
    }
};