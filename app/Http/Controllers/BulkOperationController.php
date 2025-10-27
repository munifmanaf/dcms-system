<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Collection;
use App\Services\BulkOperationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkOperationController extends Controller
{
    protected $bulkService;

    public function __construct(BulkOperationService $bulkService)
    {
        $this->bulkService = $bulkService;
    }

    public function index(Request $request)
    {
        $selectedItems = $request->get('selected_items', []);
        
        return view('bulk.index', compact('selectedItems'));
    }

    public function delete(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array',
            'selected_items.*' => 'exists:items,id',
            'confirmation' => 'required|string|in:DELETE'
        ]);

        try {
            $result = $this->bulkService->deleteItems($request->selected_items);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$result['deleted']} items",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function move(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array',
            'selected_items.*' => 'exists:items,id',
            'collection_id' => 'required|exists:collections,id'
        ]);

        try {
            $result = $this->bulkService->moveItems($request->selected_items, $request->collection_id);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully moved {$result['moved']} items",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to move items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateMetadata(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array',
            'selected_items.*' => 'exists:items,id',
            'metadata' => 'required|array'
        ]);

        try {
            $result = $this->bulkService->updateMetadata($request->selected_items, $request->metadata);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated metadata for {$result['updated']} items",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update metadata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array',
            'selected_items.*' => 'exists:items,id'
        ]);

        try {
            return $this->bulkService->downloadItems($request->selected_items);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to prepare download: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBulkStats(Request $request)
    {
        $selectedItems = $request->get('selected_items', []);
        
        if (empty($selectedItems)) {
            return response()->json([
                'total_size' => 0,
                'file_count' => 0,
                'collection_distribution' => []
            ]);
        }

        $stats = $this->bulkService->getBulkStats($selectedItems);
        
        return response()->json($stats);
    }
}