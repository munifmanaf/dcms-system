<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add data type and metadata to items table
        Schema::table('items', function (Blueprint $table) {
            $table->string('data_type')->default('document')->after('category');
            $table->string('file_extension')->nullable()->after('data_type');
            $table->string('file_size')->nullable()->after('file_extension');
            $table->string('mime_type')->nullable()->after('file_size');
            $table->json('metadata')->nullable()->after('mime_type');
            $table->string('thumbnail_path')->nullable()->after('metadata');
        });

        // Create metadata templates table
        Schema::create('metadata_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('data_type');
            $table->json('fields'); // Schema for metadata fields
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create metadata fields table for custom fields
        Schema::create('item_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->timestamps();
            
            $table->index(['item_id', 'key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_metadata');
        Schema::dropIfExists('metadata_templates');
        
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['data_type', 'file_extension', 'file_size', 'mime_type', 'metadata', 'thumbnail_path']);
        });
    }
};
