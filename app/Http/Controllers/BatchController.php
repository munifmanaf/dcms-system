<?php
// app/Http/Controllers/BatchController.php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index()
    {
        $items = Item::with(['collection.community'])->get();
        $collections = Collection::with('community')->get();
        
        $stats = [
            'totalItems' => Item::count(),
            'publishedItems' => Item::where('workflow_state', 'published')->count(),
            'pendingItems' => Item::where('workflow_state', 'pending_review')->count(),
            'draftItems' => Item::where('workflow_state', 'draft')->count(),
        ];

        return view('batch.index', array_merge(compact('items', 'collections'), $stats));
    }

    public function exportItems(Request $request)
    {
        $items = Item::with(['collection.community'])
                    ->whereIn('id', $request->item_ids ?? [])
                    ->get();

        $fileName = 'repository_export_' . date('Y-m-d') . '.csv';
        
        return response()->streamDownload(function() use ($items) {
            $handle = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($handle, [
                'ID', 'Title', 'Authors', 'Subjects', 'Description', 
                'Collection', 'Community', 'File Type', 'Status', 
                'Created Date', 'Downloads', 'Views'
            ]);

            foreach ($items as $item) {
                $metadata = $item->metadata ?? [];
                fputcsv($handle, [
                    $item->id,
                    $item->title,
                    implode('; ', $metadata['dc_creator'] ?? []),
                    implode('; ', $metadata['dc_subject'] ?? []),
                    $item->description,
                    $item->collection->name,
                    $item->collection->community->name,
                    $item->file_type,
                    $item->workflow_state,
                    $item->created_at->format('Y-m-d'),
                    $item->download_count,
                    $item->view_count
                ]);
            }
            fclose($handle);
        }, $fileName);
    }

    public function importItems(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:1024'
        ]);

        $file = $request->file('csv_file');
        $collectionId = $request->collection_id;
        
        $csvData = array_map('str_getcsv', file($file));
        $headers = array_shift($csvData);
        
        $imported = 0;
        $errors = [];

        foreach ($csvData as $row) {
            try {
                $data = array_combine($headers, $row);
                
                Item::create([
                    'title' => $data['Title'],
                    'description' => $data['Description'],
                    'file_type' => $data['File Type'] ?? 'Other',
                    'collection_id' => $collectionId,
                    'workflow_state' => 'draft',
                    'metadata' => [
                        'dc_title' => [$data['Title']],
                        'dc_creator' => $data['Authors'] ? explode(';', $data['Authors']) : [],
                        'dc_subject' => $data['Subjects'] ? explode(';', $data['Subjects']) : [],
                        'dc_description' => [$data['Description']],
                        'dc_publisher' => [config('app.name')],
                    ],
                    'user_id' => auth()->id(),
                ]);
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
            }
        }

        return back()->with([
            'success' => "Successfully imported {$imported} items",
            'errors' => $errors
        ]);
    }

    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'workflow_state' => 'required|in:draft,pending_review,published'
        ]);

        Item::whereIn('id', $request->item_ids)
            ->update(['workflow_state' => $request->workflow_state]);

        return back()->with('success', 'Bulk status update completed');
    }
}