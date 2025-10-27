<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitstreams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('internal_id')->unique();
            $table->string('mime_type');
            $table->bigInteger('size_bytes');
            $table->string('checksum');
            $table->string('checksum_algorithm')->default('MD5');
            $table->integer('sequence_id')->default(0);
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('bitstream_format_id')->constrained()->onDelete('restrict');
            $table->string('bundle_name')->default('ORIGINAL');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitstreams');
    }
};
