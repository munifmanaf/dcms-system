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
        $collections = Collection::all();

        return view('repository.browse', compact('repository', 'items', 'communities', 'collections'));
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
        $repository = Repository::first();
        
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

    public function statistics()
    {
        $repository = Repository::first();
        
        // Get real data from database
        $itemsByState = [
            'published' => Item::where('workflow_state', 'published')->count(),
            'draft' => Item::where('workflow_state', 'draft')->count(),
            'pending_review' => Item::where('workflow_state', 'pending_review')->count(),
        ];

        // Get file types from actual metadata
        $fileTypes = $this->getFileTypesFromMetadata();
        
        // Get monthly growth from actual created_at dates
        $monthlyGrowth = $this->getMonthlyGrowth();
        
        // Get top collections with real counts
        $topCollections = Collection::withCount(['items' => function($query) {
            $query->where('workflow_state', 'published');
        }])
        ->orderBy('items_count', 'desc')
        ->limit(5)
        ->get();

        // Add download stats
        $downloadStats = [
            'total_downloads' => Item::sum('download_count'),
            'total_views' => Item::sum('view_count'),
            'most_downloaded' => Item::with('collection')->orderBy('download_count', 'desc')->limit(5)->get(),
            'most_viewed' => Item::with('collection')->orderBy('view_count', 'desc')->limit(5)->get(),
            'recent_downloads' => Item::where('last_downloaded_at', '>=', now()->subDays(7))->count(),
        ];

        // Add to existing stats...
        $stats['download_stats'] = $downloadStats;
    
    return view('repository.statistics', compact('repository', 'stats'));

        $stats = [
            'total_items' => Item::count(),
            'total_communities' => Community::count(),
            'total_collections' => Collection::count(),
            'published_items' => $itemsByState['published'],
            'items_by_state' => $itemsByState,
            'file_types' => $fileTypes,
            'monthly_growth' => $monthlyGrowth,
            'top_collections' => $topCollections,
            'recent_items' => Item::where('workflow_state', 'published')
                ->with('collection.community')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return view('repository.statistics', compact('repository', 'stats'));
    }

    public function download($id)
    {
        $item = Item::findOrFail($id);
        
        // Update stats
        $item->increment('download_count');
        $item->last_downloaded_at = now();
        $item->save();
        
        return Storage::download($item->file_path);
    }

    private function getFileTypesFromMetadata()
    {
        $fileTypes = [
            'PDF' => 0,
            'Word Document' => 0,
            'Image' => 0,
            'Dataset' => 0,
            'Video' => 0,
            'Other' => 0
        ];

        // Now we can use the file_type column directly
        $items = Item::where('workflow_state', 'published')->get();
        
        foreach ($items as $item) {
            $fileType = $item->file_type ?? 'Other';
            
            if (array_key_exists($fileType, $fileTypes)) {
                $fileTypes[$fileType]++;
            } else {
                $fileTypes['Other']++;
            }
        }

        // Convert to percentages
        $total = array_sum($fileTypes);
        if ($total > 0) {
            foreach ($fileTypes as $type => $count) {
                $fileTypes[$type] = round(($count / $total) * 100);
            }
        }

        return $fileTypes;
    }

    private function getMonthlyGrowth()
    {
        $monthlyData = Item::where('workflow_state', 'published')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyGrowth = [
            'Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0,
            'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0,
            'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0
        ];

        $cumulative = 0;
        foreach ($monthlyData as $data) {
            $monthName = date('M', mktime(0, 0, 0, $data->month, 1));
            $cumulative += $data->count;
            $monthlyGrowth[$monthName] = $cumulative;
        }

        return $monthlyGrowth;
    }
}