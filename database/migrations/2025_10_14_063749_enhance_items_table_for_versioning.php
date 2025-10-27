<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Version control fields
            $table->integer('version')->default(1);
            $table->text('version_notes')->nullable();
            $table->foreignId('previous_version_id')->nullable()->constrained('items')->onDelete('set null');
            
            // File management enhancements
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            
            // Workflow fields
            $table->enum('workflow_state', ['draft', 'submitted', 'under_review', 'approved', 'published', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            // $table->timestamp('published_at')->nullable();
            
            // Indexes for performance
            $table->index('workflow_state');
            $table->index('version');
            $table->index('previous_version_id');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['previous_version_id']);
            $table->dropColumn([
                'version',
                'version_notes', 
                'previous_version_id',
                'download_count',
                'view_count',
                'workflow_state',
                'rejection_reason',
                'submitted_at',
                'reviewed_at',
                'published_at'
            ]);
        });
    }
};