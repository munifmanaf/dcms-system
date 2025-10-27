<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Community;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::with(['community', 'items'])->withCount('items')->ordered()->get();
        return view('collections.index', compact('collections'));
    }

    public function create()
    {
        $communities = Community::all();
        return view('collections.create', compact('communities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'community_id' => 'required|exists:communities,id',
        ]);

        Collection::create([
            'name' => $request->name,
            'description' => $request->description,
            'community_id' => $request->community_id,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('collections.index')->with('success', 'Collection created successfully.');
    }

    public function show(Collection $collection)
    {
        $collection->load(['community', 'items.categories', 'items.bitstreams']);
        return view('collections.show', compact('collection'));
    }

    public function edit(Collection $collection)
    {
        $communities = Community::all();
        return view('collections.edit', compact('collection', 'communities'));
    }

    public function update(Request $request, Collection $collection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'community_id' => 'required|exists:communities,id',
        ]);

        $collection->update([
            'name' => $request->name,
            'description' => $request->description,
            'community_id' => $request->community_id,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('collections.index')->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();
        return redirect()->route('collections.index')->with('success', 'Collection deleted successfully.');
    }
}