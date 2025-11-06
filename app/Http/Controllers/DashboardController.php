<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Collection;
use App\Models\Community;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_items' => Item::count(),
            'total_collections' => Collection::count(),
            'total_communities' => Community::count(),
            'published_items' => Item::where('workflow_state', 'published')->count(),
            'draft_items' => Item::where('workflow_state', 'draft')->count(),
            'pending_items' => Item::where('workflow_state', 'pending_review')->count(),
        ];

        $recentItems = Item::with('collection.community')
                          ->latest()
                          ->take(5)
                          ->get();

        return view('dashboard.index', compact('stats', 'recentItems'));
    }
}