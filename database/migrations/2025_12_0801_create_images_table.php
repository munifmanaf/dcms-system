<?php
// database/migrations/xxxx_create_images_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('path');
            $table->json('previews')->nullable(); // Store all preview paths
            $table->json('metadata')->nullable(); // Store EXIF/metadata
            $table->string('extension');
            $table->integer('size');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained()->onDelete('cascade');
            
            // For categorizing
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->text('tags')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index('category');
            $table->index('is_public');
        });
    }

    public function down()
    {
        Schema::dropIfExists('images');
    }
};