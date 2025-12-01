<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessBatchExport;
use App\Jobs\ProcessBatchImport;
use App\Exports\CollectionsExport;
use App\Exports\ItemsExport;
use App\Exports\UsersExport;
use App\Imports\CollectionsImport;
use App\Imports\UsersImport;
// use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class BatchController extends Controller
{
    /**
     * Display batch operations dashboard
     */
    public function index()
    {
        return view('batch.index');
    }

    /**
     * Show export data form
     */
    public function exportForm()
    {
        $collections = \App\Models\Collection::all();
        // dd($collections);
        return view('batch.export', compact('collections'));
    }

    /**
     * Process export request
     */
    public function export(Request $request)
    {
        $request->validate([
            'data_type' => 'required|in:collections,items,users',
            'format' => 'required|in:csv,xlsx',
            'collection_id' => 'nullable|exists:collections,id'
        ]);

        try {
            $dataType = $request->data_type;
            $format = $request->format;
            $collectionId = $request->collection_id;
            
            $fileName = "{$dataType}_export_" . now()->format('Y-m-d_H-i-s') . ".{$format}";

            switch ($dataType) {
                case 'collections':
                    return Excel::download(new CollectionsExport, $fileName);
                    
                case 'items':
                    $export = new ItemsExport;
                    if ($collectionId) {
                        $export->collectionId = $collectionId;
                    }
                    return Excel::download($export, $fileName);
                    
                case 'users':
                    return Excel::download(new UsersExport, $fileName);
                    
                default:
                    return back()->with('error', 'Invalid export type');
            }

        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Show import data form
     */
    public function importForm()
    {
        $collections = \App\Models\Collection::all();
        return view('batch.import', compact('collections'));
    }

    /**
    /**
     * Process import request - BULLETPROOF VERSION
     */
    /**
     * DIRECT UPLOAD approach
     */
   /**
     * DIRECT UPLOAD approach with duplicate checking
     */
    public function import(Request $request)
    {
        \Log::info("=== DIRECT IMPORT STARTED ===");
        
        $file = $request->file('file');
        $dataType = $request->input('data_type');
        
        // Get the file content directly from the upload
        $fileContent = file_get_contents($file->getPathname());
        \Log::info("Raw file content length: " . strlen($fileContent));
        
        // Save to a temporary file we control
        $tempPath = tempnam(sys_get_temp_dir(), 'import_');
        file_put_contents($tempPath, $fileContent);
        
        \Log::info("Temp file created: " . $tempPath);
        \Log::info("Temp file exists: " . (file_exists($tempPath) ? 'YES' : 'NO'));
        \Log::info("Temp file size: " . filesize($tempPath));
        
        // Read the temp file
        $content = file_get_contents($tempPath);
        $lines = array_filter(explode("\n", $content), function($line) {
            return trim($line) !== '';
        });
        
        \Log::info("Non-empty lines: " . count($lines));
        
        $imported = 0;
        $skipped = 0;
        $errors = [];
        
        foreach ($lines as $index => $line) {
            if ($index === 0) continue; // skip header
            
            $row = str_getcsv(trim($line));
            \Log::info("Row {$index}: " . json_encode($row));
            
            if (count($row) >= 2) {
                try {
                    if ($dataType === 'users') {
                        $result = $this->createUserWithCheck($row, $index);
                    } else {
                        $result = $this->createCollectionWithCheck($row, $index);
                    }
                    
                    if ($result === 'imported') {
                        $imported++;
                        \Log::info("✅ Created record {$imported}");
                    } elseif ($result === 'skipped') {
                        $skipped++;
                        \Log::info("⏭️ Skipped duplicate record");
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Line " . ($index + 1) . ": " . $e->getMessage();
                    \Log::error("Error creating record: " . $e->getMessage());
                }
            } else {
                $skipped++;
                \Log::warning("Skipped row {$index} - insufficient data");
            }
        }
        
        // Clean up
        unlink($tempPath);
        
        \Log::info("=== DIRECT IMPORT COMPLETED: {$imported} imported, {$skipped} skipped ===");
        
        // Prepare response message
        $message = "Import completed: {$imported} records imported";
        if ($skipped > 0) {
            $message .= ", {$skipped} records skipped (duplicates or invalid data)";
        }
        if (!empty($errors)) {
            $message .= ". " . count($errors) . " errors occurred.";
            \Log::info("Errors: " . implode('; ', $errors));
        }
        
        $messageType = !empty($errors) ? 'warning' : ($imported > 0 ? 'success' : 'info');
        
        return back()->with($messageType, $message);
    }

    /**
     * Create user with duplicate checking
     */
    private function createUserWithCheck($row, $lineIndex)
    {
        $name = trim($row[0]);
        $email = trim($row[1]);
        $password = isset($row[2]) ? trim($row[2]) : 'password123';
        
        // Validate required fields
        if (empty($name) || empty($email)) {
            throw new \Exception("Name and email are required");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format: {$email}");
        }
        
        // Check if user already exists by email
        $existingUser = \App\Models\User::where('email', $email)->first();
        if ($existingUser) {
            \Log::info("User already exists with email: {$email}");
            return 'skipped';
        }
        
        // Create the user
        $user = new \App\Models\User();
        $user->name = $name;
        $user->email = $email;
        $user->password = \Illuminate\Support\Facades\Hash::make($password);
        $user->email_verified_at = now();
        $user->save();
        
        \Log::info("Created user ID: {$user->id}, Email: {$user->email}");
        return 'imported';
    }

    /**
     * Create collection with duplicate checking
     */
    private function createCollectionWithCheck($row, $lineIndex)
    {
        $title = trim($row[0]);
        $description = isset($row[1]) ? trim($row[1]) : null;
        
        // Validate required fields
        if (empty($title)) {
            throw new \Exception("Title is required");
        }
        
        // Check if collection already exists by title
        $existingCollection = \App\Models\Collection::where('title', $title)->first();
        if ($existingCollection) {
            \Log::info("Collection already exists with title: {$title}");
            return 'skipped';
        }
        
        // Create the collection
        $collection = new \App\Models\Collection();
        $collection->title = $title;
        $collection->description = $description;
        $collection->is_public = true;
        $collection->created_by = auth()->id() ?? 1;
        $collection->save();
        
        \Log::info("Created collection ID: {$collection->id}, Title: {$collection->title}");
        return 'imported';
    }

    
    /**
     * Show bulk update form
     */
    /**
     * Show bulk update form with quick actions
     */
    public function bulkUpdateForm()
    {
        $collections = \App\Models\Collection::withCount('items')->get();
        $totalItems = \App\Models\Item::count();
        $publishedItems = \App\Models\Item::where('workflow_state', 'published')->count();
        $totalDownloads = \App\Models\Item::sum('download_count');
        $totalViews = \App\Models\Item::sum('view_count');

        return view('batch.bulk-update', compact(
            'collections', 
            'totalItems', 
            'publishedItems', 
            'totalDownloads', 
            'totalViews'
        ));
    }
    /**
     * Process bulk update request
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:status,publish,archive,collection',
            'item_ids' => 'required|string',
            'new_value' => 'required'
        ]);

        try {
            $itemIds = array_filter(explode(',', $request->item_ids));
            $action = $request->action;
            $newValue = $request->new_value;
            
            $updatedCount = 0;

            foreach ($itemIds as $itemId) {
                $item = \App\Models\Item::find(trim($itemId));
                if ($item) {
                    switch ($action) {
                        case 'status':
                            $item->workflow_state = $newValue;
                            break;
                        case 'publish':
                            $item->is_published = (bool)$newValue;
                            $item->published_at = $newValue ? now() : null;
                            break;
                        case 'archive':
                            $item->is_archived = $newValue;
                            break;
                        case 'collection':
                            $item->collection_id = $newValue;
                            break;
                    }
                    $item->save();
                    $updatedCount++;
                }
            }

            return back()->with('success', "Successfully updated {$updatedCount} items.");

        } catch (\Exception $e) {
            Log::error('Bulk update failed: ' . $e->getMessage());
            return back()->with('error', 'Bulk update failed: ' . $e->getMessage());
        }
    }

    /**
     * Get items for bulk update (AJAX)
     */
    public function getItems(Request $request)
    {
        $collectionId = $request->collection_id;
        
        $items = \App\Models\Item::when($collectionId, function($query) use ($collectionId) {
            return $query->where('collection_id', $collectionId);
        })
        ->select('id', 'title')
        ->limit(100)
        ->get();

        return response()->json($items);
    }

    /**
     * Quick publish all items in collection
     */
    public function quickPublishCollection($collectionId)
    {
        try {
            $collection = \App\Models\Collection::findOrFail($collectionId);
            $updatedCount = \App\Models\Item::where('collection_id', $collectionId)
                ->update([
                    // 'is_published' => true,
                    'published_at' => now(),
                    'workflow_state' => 'published'
                ]);

            return back()->with('success', "Successfully published {$updatedCount} items in '{$collection->title}'");

        } catch (\Exception $e) {
            Log::error('Quick publish failed: ' . $e->getMessage());
            return back()->with('error', 'Quick publish failed: ' . $e->getMessage());
        }
    }

    /**
     * Quick unpublish all items in collection
     */
    public function quickUnpublishCollection($collectionId)
    {
        try {
            $collection = \App\Models\Collection::findOrFail($collectionId);
            $updatedCount = \App\Models\Item::where('collection_id', $collectionId)
                ->update([
                    'workflow_state' => 'draft',
                    'published_at' => null
                ]);

            return back()->with('success', "Successfully unpublished {$updatedCount} items in '{$collection->title}'");

        } catch (\Exception $e) {
            Log::error('Quick unpublish failed: ' . $e->getMessage());
            return back()->with('error', 'Quick unpublish failed: ' . $e->getMessage());
        }
    }

    /**
     * Quick approve all pending items
     */
    public function quickApproveAll()
    {
        try {
            $updatedCount = \App\Models\Item::where('workflow_state', '!=', 'approved')
                ->update([
                    'workflow_state' => 'approved',
                    'is_published' => true,
                    'published_at' => now()
                ]);

            return back()->with('success', "Successfully approved and published {$updatedCount} items");

        } catch (\Exception $e) {
            Log::error('Quick approve failed: ' . $e->getMessage());
            return back()->with('error', 'Quick approve failed: ' . $e->getMessage());
        }
    }

    /**
     * Quick stats update (for demo purposes)
     */
    public function quickStatsUpdate()
    {
        try {
            // Add some random downloads and views to make stats look active
            $items = \App\Models\Item::inRandomOrder()->limit(10)->get();
            $updated = 0;

            foreach ($items as $item) {
                $item->increment('download_count', rand(1, 5));
                $item->increment('view_count', rand(5, 15));
                $item->last_viewed_at = now();
                $item->save();
                $updated++;
            }

            return back()->with('success', "Updated stats for {$updated} items - perfect for demo!");

        } catch (\Exception $e) {
            Log::error('Quick stats update failed: ' . $e->getMessage());
            return back()->with('error', 'Quick stats update failed: ' . $e->getMessage());
        }
    }
}