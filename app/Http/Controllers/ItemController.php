<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemVersion;
use App\Models\Category;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
use App\Services\ImageProcessingService;

class ItemController extends Controller
{
    protected $imageService;
    
    public function __construct(ImageProcessingService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Item::with(['collection.community', 'categories']);

        $role = Auth::user()->role;
        // dd($request);
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
        if ($request->has('workflow_state') && $request->workflow_state) {
            $isPublished = $request->workflow_state;
            $query->where('workflow_state', $isPublished);
        }

        if($request->status == 'pending_review'){
            $query->whereIn('workflow_state', ['draft', 'pending_review']);
        }elseif ($request->status == 'draft') {
            $query->where('workflow_state', 'draft');
        }
        
        // Order by latest first
        if($role == "user"){
            $query->where('user_id', Auth::id());
        }
        $query->orderBy('created_at', 'desc');
        
        $items = $query->paginate(12);
        // dd($items);
        $categories = Category::all();
        $collections = Collection::with('community')->get();
        
        return view('items.index', compact('items', 'categories', 'collections'));
    }

    public function search(Request $request)
    {
        $query = Item::where('workflow_state', 'published');
        
        // Keyword search
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                ->orWhere('metadata', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Collection filter
        if ($request->has('collection') && !empty($request->collection)) {
            $query->where('collection_id', $request->collection);
        }
        
        // File type filter
        if ($request->has('file_type') && !empty($request->file_type)) {
            $query->where('file_type', 'LIKE', "%{$request->file_type}%");
        }
        
        // Date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->where('created_at', '<=', $request->date_to);
        }
        
        // Sort options
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'popular':
                $query->orderBy('download_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $items = $query->paginate(12);
        $collections = Collection::all();
        
        return view('items.search', compact('items', 'collections'));
    }

    public function create()
    {
        $userId = auth()->id();
        $collections = Collection::with('community')->get();
        $categories = Category::all();
        $userImages = Image::where('user_id', auth()->id())
                          ->whereNull('imageable_id')
                          ->latest()
                          ->get();
        // dd($userId);
        return view('items.form', compact('collections', 'categories', 'userImages'));
    }
    

    public function store(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'collection_id' => 'required|exists:collections,id',
        //     'categories' => 'nullable|array',
        //     'categories.*' => 'exists:categories,id',
        //     'is_published' => 'boolean',
        //     'file' => 'required|file|max:10240',
        // ]);
        // dd($request);
        \Log::info('=== ALTERNATIVE UPLOAD METHOD ===');

        $file = $request->file('file');

        try {
            

             $metadata = [
                'dc_title' => [$request->title],
                'dc_creator' => $request->dc_creator ? explode(',', $request->dc_creator) : [],
                'dc_subject' => $request->dc_subject ? explode(',', $request->dc_subject) : [],
                'dc_description' => $request->dc_description ? [$request->dc_description] : [],
                'dc_publisher' => $request->dc_publisher ? [$request->dc_publisher] : [],
                'dc_date_issued' => $request->dc_date_issued ? [$request->dc_date_issued] : [],
                'dc_type' => $request->dc_type ? [$request->dc_type] : [],
                'dc_format' => $request->dc_format ? [$request->dc_format] : [],
                'dc_identifier' => $request->dc_identifier ? [$request->dc_identifier] : [],
            ];

            $addData = [
                'title' => $request->title,
                'description' => $request->description,
                'collection_id' => $request->collection_id,
                'is_published' => $request->boolean('is_published', false),
                'metadata' => $metadata,
                'user_id' => auth()->id(),
                // 'file_path' => $filePath,
                // 'file_name' => $originalName,
                // 'file_size' => $storedFileSize,
                // 'file_type' => $storedMimeType ?: $file->getClientMimeType(),
            ];

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

                $file = $request->file('file');
                $originalExtension = strtolower($file->getClientOriginalExtension());

                $extensionMap = [
                    'xlsx' => 'Dataset',
                    'xls' => 'Dataset', 
                    'csv' => 'Dataset',
                    'pdf' => 'PDF',
                    'doc' => 'Word Document',
                    'docx' => 'Word Document',
                    'jpg' => 'Image',
                    'jpeg' => 'Image',
                    'png' => 'Image',
                    'gif' => 'Image',
                    'mp4' => 'Video',
                    'avi' => 'Video',
                ];
                // dd($extensionMap);
                $fileType = $extensionMap[$originalExtension] ?? 'Other';
                // Add file data to update
                $addData['file_path'] = $filePath;
                $addData['file_name'] = $originalName;
                $addData['file_size'] = $storedFileSize;
                $addData['file_type'] = $fileType;
                // 
                // Delete old file
                // $this->deleteOldFile($item->file_path);
            }
            // dd($addData);
            // Create item
            $item = Item::create($addData);
            // dd($item);
            if($item){
                dd('1');
            }else{
                dd('babi');
            }
            // $item->save();

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
        // dd(is_array($item->metadata));
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $item->load(['categories']);
        $collections = Collection::with('community')->get();
        $categories = Category::all();
        // dd($item);
        return view('items.form', compact('item', 'collections', 'categories'));
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
            // 'changes' => 'nullable|string|max:500', 
            // What changed in this update
        ]);
        // dd($request);
        try {
            // $item->createVersion($request->changes);
            // Build metadata
            
            // ... your metadata building code ...
            $metadata = [
                'dc_title' => [$request->title],
                'dc_creator' => $request->dc_creator ? array_map('trim', explode(',', $request->dc_creator)) : [],
                'dc_subject' => $request->dc_subject ? array_map('trim', explode(',', $request->dc_subject)) : [],
                'dc_description' => $request->dc_description ? [$request->dc_description] : [],
                'dc_publisher' => $request->dc_publisher ? [$request->dc_publisher] : [],
                'dc_date_issued' => $request->dc_date_issued ? [$request->dc_date_issued] : [],
                'dc_type' => $request->dc_type ? [$request->dc_type] : [],
                'dc_format' => $request->dc_format ? [$request->dc_format] : [],
                'dc_identifier' => $request->dc_identifier ? [$request->dc_identifier] : [],
            ];

            // dd($metadata);
            $updateData = [
                'title' => isset($request->title) ? $request->title : $item->title,
                'description' => isset($request->description) ? $request->description : $item->description,
                'collection_id' =>isset($request->collection_id) ? $request->collection_id : $item->collection_id,
                // 'is_published' => isset($request->boolean('is_published', false)) ? $request->boolean('is_published', false) : 'true' ,
                'metadata' => $metadata,
                'workflow_state' => isset($request->workflow_state) ? $request->workflow_state : $item->workflow_state
            ];
            // dd($updateData);
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

                $file = $request->file('file');
                $originalExtension = strtolower($file->getClientOriginalExtension());

                $extensionMap = [
                    'xlsx' => 'Dataset',
                    'xls' => 'Dataset', 
                    'csv' => 'Dataset',
                    'pdf' => 'PDF',
                    'doc' => 'Word Document',
                    'docx' => 'Word Document',
                    'jpg' => 'Image',
                    'jpeg' => 'Image',
                    'png' => 'Image',
                    'gif' => 'Image',
                    'mp4' => 'Video',
                    'avi' => 'Video',
                ];

                $fileType = $extensionMap[$originalExtension] ?? 'Other';
                // Add file data to update
                $updateData['file_path'] = $filePath;
                $updateData['file_name'] = $originalName;
                $updateData['file_size'] = $storedFileSize;
                $updateData['file_type'] = $fileType;

                // Delete old file
                $this->deleteOldFile($item->file_path);
            }
            // dd($updateData);
            // Update the item
            Item::where('id', $item->id) // Make sure you have the fresh instance
                    ->update($updateData);
            
            // Handle categories
            // if ($request->has('categories')) {
            //     $item->categories()->sync($request->categories);
            // } else {
            //     $item->categories()->detach();
            // }

            return redirect()->route('items.index')
                ->with('success', 'Item updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Item update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update item: ' . $e->getMessage());
        }
    }

    public function status(Request $request, Item $item)
    {
        $updateStatus =[
            'workflow_state' => $request->workflow_state
        ];

        if($request->workflow_state == 'published'){
            $updateStatus['is_published'] = true;
        }elseif($request->workflow_state == 'draft'){
            $updateStatus['is_published'] = false;
        }
        // dd($updateStatus);
        $up = Item::where('id', $item->id) // Make sure you have the fresh instance
                    ->update($updateStatus);

        if($up){
            return redirect()->route('items.show', $item->id)
                ->with('success', 'Status updated successfully.');
        }else{
            return redirect()->route('items.show', $item->id)
                ->with('error', 'Status not updated successfully.');
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