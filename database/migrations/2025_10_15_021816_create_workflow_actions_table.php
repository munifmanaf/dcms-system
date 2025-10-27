<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workflow_step_id')->constrained()->onDelete('cascade');
            $table->string('action'); // The action taken
            $table->text('comments')->nullable(); // Reviewer comments
            $table->string('status'); // approved, rejected, returned
            $table->json('metadata')->nullable(); // Additional action data
            $table->timestamps();
            
            $table->index('item_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_actions');
    }
};
