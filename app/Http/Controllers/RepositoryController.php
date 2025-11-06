<?php
// app/Http/Controllers/RepositoryController.php

namespace App\Http\Controllers;

use App\Models\Repository;
use App\Models\Community;
use App\Models\Collection;
use App\Models\Item;
use Illuminate\Http\Request;

class RepositoryController extends Controller
{
    /**
     * Display repository homepage (public)
     */
    public function index()
    {
        $repository = Repository::first(); // Just get first repository
        if (!$repository) {
            // Create a default repository if none exists
            $repository = Repository::create([
                'name' => 'Institutional Repository',
                'description' => 'Digital repository for research outputs',
                'handle_prefix' => '123456789'
            ]);
        }
        
        $recentItems = Item::where('workflow_state', 'published')
                          ->with(['collection.community'])
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
        
        $communities = Community::withCount(['collections'])->get();

        return view('repository.index', compact('repository', 'recentItems', 'communities'));
    }

    /**
     * Browse items in repository
     */
    public function browse(Request $request)
    {
        $repository = Repository::first();
        
        $query = Item::with(['collection.community'])
                    ->where('workflow_state', 'published');
        
        // Search
        if ($request->has('q') && $request->q) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%')
                  ->orWhere('metadata', 'like', '%' . $request->q . '%');
            });
        }
        
        // Filter by community
        if ($request->has('community_id') && $request->community_id) {
            $query->whereHas('collection', function($q) use ($request) {
                $q->where('community_id', $request->community_id);
            });
        }
        
        // Filter by collection
        if ($request->has('collection_id') && $request->collection_id) {
            $query->where('collection_id', $request->collection_id);
        }
        
        $items = $query->orderBy('created_at', 'desc')->paginate(20);
        $communities = Community::with('collections')->get();

        return view('repository.browse', compact('repository', 'items', 'communities'));
    }

    /**
     * Show a single item (public)
     */
    public function showItem($id)
    {
        $repository = Repository::first();
        $item = $repository->items()
                          ->with(['collection.community'])
                          ->where('workflow_state', 'published')
                          ->findOrFail($id);

        // Generate DSpace-like handle
        $handle = $repository->generateHandle($item->id);

        return view('repository.item', compact('repository', 'item', 'handle'));
    }

    /**
     * Show items in a community
     */
    public function showCommunity($id)
    {
        $repository = Repository::first();
        $community = $repository->communities()->with(['collections.items'])->findOrFail($id);
        $items = $community->items()->where('workflow_state', 'published')->paginate(20);

        return view('repository.community', compact('repository', 'community', 'items'));
    }

    /**
     * Show items in a collection
     */
    public function showCollection($id)
    {
        $repository = Repository::first(); // Remove getActive() since no is_active column
        
        $collection = Collection::with('community')->findOrFail($id);
        
        // Remove the repository_id check since communities might not have repository_id yet
        // $collection = Collection::whereHas('community', function($q) use ($repository) {
        //     $q->where('repository_id', $repository->id);
        // })->with('community')->findOrFail($id);

        $items = $collection->items()->where('workflow_state', 'published')->paginate(20);
        // Changed 'status' to 'workflow_state'

        return view('repository.collection', compact('repository', 'collection', 'items'));
    }


    // ADMIN METHODS

    /**
     * Show repository settings (admin)
     */
    public function settings()
    {
        $repository = Repository::getActive();
        return view('admin.repository.settings', compact('repository'));
    }

    /**
     * Update repository settings
     */
    public function updateSettings(Request $request)
    {
        $repository = Repository::getActive();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'handle_prefix' => 'required|string|max:50',
            'contact_email' => 'nullable|email',
            'copyright_text' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('repository', 'public');
            $validated['logo'] = $path;
        }

        $repository->update($validated);

        return redirect()->route('admin.repository.settings')
                         ->with('success', 'Repository settings updated successfully.');
    }

    // app/Http/Controllers/RepositoryController.php
    public function statistics()
    {
        $repository = Repository::getActive();
        
        $stats = [
            'total_items' => $repository->items()->where('status', 'published')->count(),
            'total_communities' => $repository->communities()->count(),
            'total_collections' => $repository->collections()->count(),
            'top_collections' => Collection::withCount('items')
                ->orderBy('items_count', 'desc')
                ->limit(5)
                ->get(),
            'monthly_growth' => $this->getMonthlyGrowth(),
            'file_types' => $this->getFileTypeDistribution()
        ];

        return view('repository.statistics', compact('repository', 'stats'));
    }
}