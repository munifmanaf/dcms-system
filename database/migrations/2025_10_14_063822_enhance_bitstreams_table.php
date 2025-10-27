<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bitstreams', function (Blueprint $table) {
            // File versioning
            $table->integer('file_version')->default(1);
            $table->boolean('is_current')->default(true);
            $table->foreignId('replaces_bitstream_id')->nullable()->constrained('bitstreams')->onDelete('set null');
            
            // Download tracking
            $table->integer('download_count')->default(0);
            
            // File metadata
            $table->string('original_filename');
            $table->string('file_extension');
            $table->text('technical_metadata')->nullable(); // JSON for technical info
            
            // Indexes
            $table->index('is_current');
            $table->index('file_version');
            $table->index('replaces_bitstream_id');
        });
    }

    public function down(): void
    {
        Schema::table('bitstreams', function (Blueprint $table) {
            $table->dropForeign(['replaces_bitstream_id']);
            $table->dropColumn([
                'file_version',
                'is_current',
                'replaces_bitstream_id', 
                'download_count',
                'original_filename',
                'file_extension',
                'technical_metadata'
            ]);
        });
    }
};