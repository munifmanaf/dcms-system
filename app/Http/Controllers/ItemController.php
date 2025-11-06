<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemVersion;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['collection.community', 'categories']);
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }
        
        // Filter by collection
        if ($request->has('collection') && $request->collection) {
            $query->where('collection_id', $request->collection);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $isPublished = $request->status === 'published';
            $query->where('is_published', $isPublished);
        }
        
        // Order by latest first
        $query->orderBy('created_at', 'desc');
        
        $items = $query->paginate(12);
        
        $categories = Category::all();
        $collections = Collection::with('community')->get();
        
        return view('items.index', compact('items', 'categories', 'collections'));
    }

    public function create()
    {
        $userId = auth()->id();
        $collections = Collection::with('community')->get();
        $categories = Category::all();
        // dd($userId);
        return view('items.create', compact('collections', 'categories'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'collection_id' => 'required|exists:collections,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'is_published' => 'boolean',
            'file' => 'required|file|max:10240',
        ]);

        \Log::info('=== ALTERNATIVE UPLOAD METHOD ===');

        if (!$request->hasFile('file')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No file was uploaded.');
        }

        $file = $request->file('file');

        try {
            $originalName = $file->getClientOriginalName();
            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $fileName = time() . '_' . $safeName;
            $filePath = 'items/' . $fileName;

            // METHOD 1: Use file_get_contents() and Storage::put()
            $fileContent = file_get_contents($file->getPathname());
            
            if ($fileContent === false) {
                throw new \Exception('Could not read file content');
            }

            // Store using Laravel Storage
            \Storage::disk('public')->put($filePath, $fileContent);
            
            \Log::info('File stored via content method: ' . $filePath);

            // Verify the file was stored
            if (!\Storage::disk('public')->exists($filePath)) {
                throw new \Exception('File storage failed');
            }

            // Get file info from stored file
            $storedFileSize = \Storage::disk('public')->size($filePath);
            $storedMimeType = \Storage::disk('public')->mimeType($filePath);

            // Build metadata
            // $metadata = [];
            // if ($request->filled('metadata.author')) {
            //     $metadata['author'] = $request->metadata['author'];
            // }
            // if ($request->filled('metadata.year')) {
            //     $metadata['year'] = $request->metadata['year'];
            // }
            // if ($request->filled('metadata.language')) {
            //     $metadata['language'] = $request->metadata['language'];
            // }
            // if ($request->filled('metadata.pages')) {
            //     $metadata['pages'] = $request->metadata['pages'];
            // }
            
            // if ($request->has('metadata_keys')) {
            //     foreach ($request->metadata_keys as $index => $key) {
            //         $value = $request->metadata_values[$index] ?? '';
            //         if (!empty(trim($key)) && !empty(trim($value))) {
            //             $metadata[trim($key)] = trim($value);
            //         }
            //     }
            // }

            // When saving items, structure metadata like DSpace:


            // Create item
            $item = Item::create([
                'title' => $request->title,
                'description' => $request->description,
                'collection_id' => $request->collection_id,
                'is_published' => $request->boolean('is_published', false),
                // 'metadata' => $metadata,
                'user_id' => auth()->id(),
                'file_path' => $filePath,
                'file_name' => $originalName,
                'file_size' => $storedFileSize,
                'file_type' => $storedMimeType ?: $file->getClientMimeType(),
            ]);

            $metadata = [
                'dc_title' => [$request->title],
                'dc_creator' => is_array($request->creators) ? $request->creators : [],
                'dc_subject' => is_array($request->subjects) ? $request->subjects : [],
                'dc_description' => [$request->description],
                'dc_date_issued' => [$request->date_issued],
                'dc_type' => [$request->type],
                'dc_identifier' => ['item-' . $item->id],
            ];

            $item->metadata = $metadata;
            $item->save();

            if ($request->has('categories')) {
                $item->categories()->sync($request->categories);
            }

            return redirect()->route('items.index')
                ->with('success', 'Item created successfully.');

        } catch (\Exception $e) {
            \Log::error('Alternative upload error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to upload file: ' . $e->getMessage());
        }
    }

    public function show(Item $item)
    {
        $item->load(['collection.community', 'categories']);
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $item->load(['categories']);
        $collections = Collection::with('community')->get();
        $categories = Category::all();
        // dd($item);
        return view('items.edit', compact('item', 'collections', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'collection_id' => 'required|exists:collections,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'is_published' => 'boolean',
            'file' => 'nullable|file|max:10240',
            'changes' => 'nullable|string|max:500', // What changed in this update
        ]);

        try {
            $item->createVersion($request->changes);
            // Build metadata
            $metadata = [];
            // ... your metadata building code ...

            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'collection_id' => $request->collection_id,
                'is_published' => $request->boolean('is_published', false),
                'metadata' => $metadata,
            ];

            // Handle file upload using the method that worked for you
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
                $fileName = time() . '_' . $safeName;
                $filePath = 'items/' . $fileName;

                // Use the approach that worked in your store method
                $fileContent = file_get_contents($file->getPathname());
                
                if ($fileContent === false) {
                    throw new \Exception('Could not read file content');
                }

                // Store using Laravel Storage
                \Storage::disk('public')->put($filePath, $fileContent);
                
                \Log::info('New file stored via content method: ' . $filePath);

                // Verify the file was stored
                if (!\Storage::disk('public')->exists($filePath)) {
                    throw new \Exception('File storage failed during update');
                }

                // Get file info from stored file
                $storedFileSize = \Storage::disk('public')->size($filePath);
                $storedMimeType = \Storage::disk('public')->mimeType($filePath);

                // Add file data to update
                $updateData['file_path'] = $filePath;
                $updateData['file_name'] = $originalName;
                $updateData['file_size'] = $storedFileSize;
                $updateData['file_type'] = $storedMimeType ?: $file->getClientMimeType();

                // Delete old file
                $this->deleteOldFile($item->file_path);
            }

            // Update the item
            $item->update($updateData);

            // Handle categories
            if ($request->has('categories')) {
                $item->categories()->sync($request->categories);
            } else {
                $item->categories()->detach();
            }

            return redirect()->route('items.index')
                ->with('success', 'Item updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Item update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update item: ' . $e->getMessage());
        }
    }

    /**
     * Delete old file from storage
     */
    private function deleteOldFile($filePath)
    {
        try {
            if ($filePath && \Storage::disk('public')->exists($filePath)) {
                \Storage::disk('public')->delete($filePath);
                \Log::info('Deleted old file: ' . $filePath);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete old file: ' . $e->getMessage());
            // Don't throw error, just log it
        }
    }

    public function destroy(Item $item)
    {
        // Delete associated file from public storage if exists
        if ($item->file_path && Storage::disk('public')->exists($item->file_path)) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Item deleted successfully.');
    }

    public function downloadFile(Item $item)
    {
        if (!$item->file_path) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Check if file exists in public storage
        if (!Storage::disk('public')->exists($item->file_path)) {
            return redirect()->back()->with('error', 'File does not exist.');
        }

        // Download from public storage
        return Storage::disk('public')->download($item->file_path, $item->file_name);
    }

    public function preview(Item $item)
    {
        if (!Storage::disk('public')->exists($item->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($item->file_path));
    }

    // ItemController.php
    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,move',
            'items' => 'required|array',
            'items.*' => 'exists:items,id'
        ]);

        try {
            switch ($request->action) {
                case 'delete':
                    Item::whereIn('id', $request->items)->delete();
                    break;
                case 'publish':
                    Item::whereIn('id', $request->items)->update(['is_published' => true]);
                    break;
                case 'unpublish':
                    Item::whereIn('id', $request->items)->update(['is_published' => false]);
                    break;
                case 'move':
                    if ($request->has('collection_id')) {
                        Item::whereIn('id', $request->items)
                            ->update(['collection_id' => $request->collection_id]);
                    }
                    break;
            }

            return redirect()->back()->with('success', 'Bulk action completed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    public function versions(Item $item)
    {
        $versions = $item->manualVersions()->with('user')->paginate(10);
        
        return view('items.versions', compact('item', 'versions'));
    }

    /**
     * Compare two versions
     */
    public function compareVersions(Item $item, ItemVersion $version1, ItemVersion $version2 = null)
    {
        $version2 = $version2 ?? $item;
        $differences = $version1->compareWith($version2);
        
        return view('items.compare', compact('item', 'version1', 'version2', 'differences'));
    }

    /**
     * Restore item from version
     */
    public function restoreVersion(Item $item, ItemVersion $version)
    {
        $item->restoreFromVersion($version);
        
        return redirect()->route('items.show', $item)
            ->with('success', "Item restored from version {$version->version_number} successfully.");
    }

    /**
     * Download version file
     */
    public function downloadVersion(ItemVersion $version)
    {
        if (!$version->hasFile()) {
            abort(404, 'File not found for this version.');
        }

        return Storage::disk('public')->download($version->file_path, $version->file_name);
    }

    /**
     * Delete a version (keep the file if other versions use it)
     */
    public function destroyVersion(Item $item, ItemVersion $version)
    {
        // Don't allow deleting if it's the only version
        if ($item->manualVersions()->count() <= 1) {
            return redirect()->back()
                ->with('error', 'Cannot delete the only version of an item.');
        }

        $version->delete();
        
        return redirect()->route('items.versions', $item)
            ->with('success', 'Version deleted successfully.');
    }
}