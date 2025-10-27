<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function index()
    {
        $communities = Community::withCount(['collections', 'collections as items_count' => function($query) {
            $query->join('items', 'collections.id', '=', 'items.collection_id');
        }])->ordered()->get();
        
        return view('communities.index', compact('communities'));
    }

    public function create()
    {
        return view('communities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Community::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('communities.index')->with('success', 'Community created successfully.');
    }

    public function show(Community $community)
    {
        $community->load(['collections.items']);
        return view('communities.show', compact('community'));
    }

    public function edit(Community $community)
    {
        return view('communities.edit', compact('community'));
    }

    public function update(Request $request, Community $community)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $community->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('communities.index')->with('success', 'Community updated successfully.');
    }

    public function destroy(Community $community)
    {
        $community->delete();
        return redirect()->route('communities.index')->with('success', 'Community deleted successfully.');
    }
}