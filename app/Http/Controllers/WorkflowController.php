<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    /**
     * Show workflow interface for an item
     */
    public function show(Item $item)
    {
        // Eager load all necessary relationships
        $item->load([
            'collection.community', 
            'categories', 
            'user', // This will now work
            'approvedByUser' // Load the approver user as well
        ]);
        
        return view('workflow.show', compact('item'));
    }

    /**
     * Submit item for review
     */
    public function submit(Request $request, Item $item)
    {
        // If you have a workflow_status field, use it
        $item->update([
            // 'workflow_status' => 'submitted', // Uncomment if you have this field
            'submitted_at' => now(),
            // 'submitted_by' => Auth::id(), // Uncomment if you have this field
        ]);

        return redirect()->back()->with('success', 'Item submitted for review successfully.');
    }

    /**
     * Technical review action
     */
    public function technicalReview(Request $request, Item $item)
    {
        $item->update([
            // 'workflow_status' => 'technical_review', // Uncomment if you have this field
            'technical_reviewed_at' => now(),
            // 'technical_reviewed_by' => Auth::id(), // Uncomment if you have this field
        ]);

        return redirect()->back()->with('success', 'Technical review completed.');
    }

    /**
     * Content review action
     */
    public function contentReview(Request $request, Item $item)
    {
        $item->update([
            // 'workflow_status' => 'content_review', // Uncomment if you have this field
            'content_reviewed_at' => now(),
            // 'content_reviewed_by' => Auth::id(), // Uncomment if you have this field
        ]);

        return redirect()->back()->with('success', 'Content review completed.');
    }

    /**
     * Final approval
     */
    public function finalApprove(Request $request, Item $item)
    {
        $item->update([
            'is_approved' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            // 'workflow_status' => 'approved', // Uncomment if you have this field
        ]);

        return redirect()->back()->with('success', 'Item finally approved.');
    }

    /**
     * Quick approval
     */
    public function quickApprove(Request $request, Item $item)
    {
        $item->update([
            'is_approved' => true,
            'is_published' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            // 'workflow_status' => 'published', // Uncomment if you have this field
        ]);

        return redirect()->back()->with('success', 'Item quickly approved and published.');
    }
}