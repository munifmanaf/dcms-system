<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // OAI-PMH Identification
            $table->string('oai_identifier')->nullable()->unique()->after('id');
            $table->string('oai_datestamp')->nullable()->after('oai_identifier');
            
            // Harvest Information
            $table->foreignId('harvest_log_id')->nullable()->after('collection_id')
                ->constrained('oai_harvest_logs')->nullOnDelete();
            $table->timestamp('import_date')->nullable()->after('harvest_log_id');
            $table->enum('source', ['manual', 'oai-pmh', 'csv-import'])->default('manual')->after('import_date');
            
            // For better querying of OAI items
            $table->string('accession_number')->nullable()->after('source');
            $table->string('external_identifier')->nullable()->after('accession_number');
            
            // Add index for performance
            $table->index(['oai_identifier']);
            $table->index(['harvest_log_id']);
            $table->index(['source']);
            $table->index(['import_date']);
            
            // Note: metadata column already exists, so we'll use it for DC metadata
        });

    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['oai_identifier']);
            $table->dropIndex(['harvest_log_id']);
            $table->dropIndex(['source']);
            $table->dropIndex(['import_date']);
            
            $table->dropColumn([
                'oai_identifier',
                'oai_datestamp',
                'harvest_log_id',
                'import_date',
                'source',
                'accession_number',
                'external_identifier'
            ]);
        });
    }
};