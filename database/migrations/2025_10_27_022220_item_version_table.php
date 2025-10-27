<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('version_number'); // e.g., "1.0", "1.1", "2.0"
            $table->string('file_path')->nullable(); // Path to versioned file
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Store metadata at time of version
            $table->text('changes')->nullable(); // What changed in this version
            $table->foreignId('user_id')->constrained(); // Who created this version
            $table->boolean('is_autosave')->default(false); // Whether this is an autosave version
            $table->foreignId('restored_from_id')->nullable()->constrained('item_versions'); // If restored from another version
            $table->timestamps();

            // Indexes for performance
            $table->index(['item_id', 'version_number']);
            $table->index(['item_id', 'is_autosave']);
            $table->index(['item_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_versions');
    }
};