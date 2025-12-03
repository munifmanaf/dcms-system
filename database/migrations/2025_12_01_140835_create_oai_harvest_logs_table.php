
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oai_harvest_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->string('metadata_prefix')->default('oai_dc');
            $table->string('set_spec')->nullable();
            $table->dateTime('from_date')->nullable();
            $table->dateTime('until_date')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->integer('total_records')->default(0);
            $table->integer('imported_records')->default(0);
            $table->integer('skipped_records')->default(0);
            $table->integer('failed_records')->default(0);
            $table->text('resumption_token')->nullable();
            $table->text('error_message')->nullable();
            $table->json('parameters')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oai_harvest_logs');
    }
};