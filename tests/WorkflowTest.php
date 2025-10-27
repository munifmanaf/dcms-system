<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\WorkflowStep;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_workflow_cycle()
    {
        // Create test users with different roles
        $submitter = User::factory()->create(['role' => 'user']);
        $techReviewer = User::factory()->create(['role' => 'technical_reviewer']);
        $contentReviewer = User::factory()->create(['role' => 'content_reviewer']);
        $manager = User::factory()->create(['role' => 'manager']);

        // Create workflow steps
        $steps = [
            ['name' => 'Submit', 'action' => 'submit', 'role' => 'user'],
            ['name' => 'Technical Review', 'action' => 'technical_review', 'role' => 'technical_reviewer'],
            ['name' => 'Content Review', 'action' => 'content_review', 'role' => 'content_reviewer'],
            ['name' => 'Publish', 'action' => 'publish', 'role' => 'manager'],
        ];

        foreach ($steps as $step) {
            WorkflowStep::create($step);
        }

        // Test item submission
        $this->actingAs($submitter);
        $item = Item::factory()->create(['workflow_state' => 'draft']);

        $response = $this->post(route('workflow.submit', $item), [
            'comments' => 'Ready for review'
        ]);

        $response->assertRedirect();
        $this->assertEquals('submitted', $item->fresh()->workflow_state);

        // Continue with other workflow steps...
    }
}