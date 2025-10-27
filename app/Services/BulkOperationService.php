<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BulkOperationService
{
    public function deleteItems(array $itemIds)
    {
        return DB::transaction(function () use ($itemIds) {
            $deleted = 0;
            $errors = [];

            foreach ($itemIds as $itemId) {
                try {
                    $item = Item::withTrashed()->find($itemId);
                    
                    if ($item) {
                        // Soft delete the item
                        $item->delete();
                        $deleted++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'item_id' => $itemId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return [
                'deleted' => $deleted,
                'errors' => $errors,
                'total' => count($itemIds)
            ];
        });
    }

    public function moveItems(array $itemIds, $collectionId)
    {
        return DB::transaction(function () use ($itemIds, $collectionId) {
            $moved = 0;
            $errors = [];

            $collection = Collection::findOrFail($collectionId);

            foreach ($itemIds as $itemId) {
                try {
                    $item = Item::find($itemId);
                    
                    if ($item) {
                        $item->collection_id = $collectionId;
                        $item->save();
                        $moved++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'item_id' => $itemId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return [
                'moved' => $moved,
                'errors' => $errors,
                'total' => count($itemIds),
                'collection_name' => $collection->name
            ];
        });
    }

    public function updateMetadata(array $itemIds, array $metadata)
    {
        return DB::transaction(function () use ($itemIds, $metadata) {
            $updated = 0;
            $errors = [];

            foreach ($itemIds as $itemId) {
                try {
                    $item = Item::find($itemId);
                    
                    if ($item) {
                        // Update metadata
                        $currentMetadata = $item->metadata ?? [];
                        $item->metadata = array_merge($currentMetadata, $metadata);
                        $item->save();
                        $updated++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'item_id' => $itemId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return [
                'updated' => $updated,
                'errors' => $errors,
                'total' => count($itemIds)
            ];
        });
    }

    public function downloadItems(array $itemIds)
    {
        $items = Item::whereIn('id', $itemIds)->get();
        
        if ($items->count() === 1) {
            // Single file download
            $item = $items->first();
            return Storage::download($item->file_path, $item->filename);
        }

        // Multiple files - create zip
        $zip = new ZipArchive;
        $zipFileName = 'bulk_download_' . now()->format('Y_m_d_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($items as $item) {
                if (Storage::exists($item->file_path)) {
                    $zip->addFile(
                        Storage::path($item->file_path),
                        $item->filename
                    );
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function getBulkStats(array $itemIds)
    {
        $items = Item::whereIn('id', $itemIds)->get();
        
        $totalSize = $items->sum('file_size');
        $fileCount = $items->count();
        
        $collectionDistribution = $items->groupBy('collection_id')
            ->map(function ($group) {
                return $group->count();
            });

        return [
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'file_count' => $fileCount,
            'collection_distribution' => $collectionDistribution
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}