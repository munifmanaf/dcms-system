// database/migrations/xxxx_xx_xx_create_images_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('original_name');
            $table->string('stored_name');
            $table->json('paths'); // Store all image paths (original, thumbnail, medium, large)
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->json('metadata')->nullable(); // EXIF data
            $table->json('dimensions')->nullable(); // Width and height
            $table->boolean('has_watermark')->default(false);
            $table->boolean('is_optimized')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('images');
    }
};
