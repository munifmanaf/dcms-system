<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metadata_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->enum('type', [
                'text', 
                'textarea', 
                'number', 
                'date', 
                'email', 
                'url', 
                'select', 
                'multiselect', 
                'boolean',
                'file'
            ])->default('text');
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // For select/multiselect options
            $table->string('default_value')->nullable();
            $table->string('validation_rules')->nullable(); // Custom validation rules
            $table->text('help_text')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint to prevent duplicate field names in same collection
            $table->unique(['collection_id', 'slug']);
            
            // Index for ordering and filtering
            $table->index(['collection_id', 'order', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metadata_fields');
    }
};