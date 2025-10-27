<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Submission', 'Technical Review', 'Content Review'
            $table->string('action'); // e.g., 'submit', 'technical_review', 'content_review'
            $table->integer('order')->default(0);
            $table->json('allowed_roles')->nullable(); // Roles that can perform this step
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('order');
            $table->index('is_active');
        });

        // Insert default workflow steps
        DB::table('workflow_steps')->insert([
            [
                'name' => 'Draft',
                'action' => 'save_draft',
                'order' => 0,
                'allowed_roles' => json_encode(['user', 'manager', 'admin']),
                'description' => 'Item is being prepared by submitter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Submission',
                'action' => 'submit',
                'order' => 1,
                'allowed_roles' => json_encode(['user', 'manager', 'admin']),
                'description' => 'Item submitted for review',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Technical Review',
                'action' => 'technical_review',
                'order' => 2,
                'allowed_roles' => json_encode(['reviewer', 'manager', 'admin']),
                'description' => 'Technical aspects review',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Content Review',
                'action' => 'content_review',
                'order' => 3,
                'allowed_roles' => json_encode(['reviewer', 'manager', 'admin']),
                'description' => 'Content quality review',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Approval',
                'action' => 'approve',
                'order' => 4,
                'allowed_roles' => json_encode(['manager', 'admin']),
                'description' => 'Final approval for publication',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Published',
                'action' => 'publish',
                'order' => 5,
                'allowed_roles' => json_encode(['manager', 'admin']),
                'description' => 'Item is published and publicly available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};