<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitstream_formats', function (Blueprint $table) {
            $table->id();
            $table->string('mimetype');
            $table->string('short_description');
            $table->text('description')->nullable();
            $table->string('support_level')->default('UNKNOWN');
            $table->boolean('internal')->default(false);
            $table->text('extensions')->nullable();
            $table->timestamps();
        });

        // Insert default data
        DB::table('bitstream_formats')->insert([
            ['mimetype' => 'application/pdf', 'short_description' => 'Adobe PDF', 'support_level' => 'SUPPORTED', 'extensions' => 'pdf', 'created_at' => now(), 'updated_at' => now()],
            ['mimetype' => 'application/msword', 'short_description' => 'Microsoft Word', 'support_level' => 'SUPPORTED', 'extensions' => 'doc,docx', 'created_at' => now(), 'updated_at' => now()],
            ['mimetype' => 'text/plain', 'short_description' => 'Plain Text', 'support_level' => 'SUPPORTED', 'extensions' => 'txt', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bitstream_formats');
    }
};