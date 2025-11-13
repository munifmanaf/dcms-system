<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $stats = $this->getSystemStats();
        return view('reports.index', compact('stats'));
    }

    public function usageStats()
    {
        $usageData = $this->getUsageStatistics();
        return view('reports.usage', compact('usageData'));
    }

    public function exportReport(Request $request)
    {
        $type = $request->get('type', 'usage');
        $filename = "dcms_report_{$type}_" . now()->format('Y-m-d') . ".csv";
        
        return response()->streamDownload(function () use ($type) {
            $this->generateCSVReport($type);
        }, $filename);
    }

    private function getSystemStats()
    {
        return [
            'total_items' => Item::count(),
            'published_items' => Item::where('workflow_state', 'published')->count(),
            'total_collections' => Collection::count(),
            'total_users' => User::count(),
            'total_downloads' => Item::sum('download_count'),
            'total_views' => Item::sum('view_count'),
            'storage_usage' => $this->calculateStorageUsage(),
            'popular_items' => Item::where('workflow_state', 'published')
                                ->orderBy('download_count', 'desc')
                                ->limit(5)
                                ->get(),
            'recent_items' => Item::where('workflow_state', 'published')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get()
        ];
    }

    private function getUsageStatistics()
    {
        return [
            'downloads_by_month' => DB::table('items')
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(download_count) as downloads'))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get(),
            'views_by_month' => DB::table('items')
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(view_count) as views'))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get(),
            'top_downloaded' => Item::where('workflow_state', 'published')
                                ->orderBy('download_count', 'desc')
                                ->limit(10)
                                ->get(),
            'top_viewed' => Item::where('workflow_state', 'published')
                                ->orderBy('view_count', 'desc')
                                ->limit(10)
                                ->get()
        ];
    }

    // In ReportController class, update these methods:

    public function collectionReport()
    {
        $collections = Collection::withCount('items')->get();
        $collectionStats = $this->getCollectionStats();
        
        return view('reports.collections', compact('collections', 'collectionStats'));
    }

    private function getCollectionStats()
    {
        return Collection::withCount('items')
            ->with(['items' => function($query) {
                $query->select('collection_id', 
                    DB::raw('SUM(download_count) as total_downloads'),
                    DB::raw('SUM(view_count) as total_views')
                )->groupBy('collection_id');
            }])
            ->get()
            ->map(function($collection) {
                $totalDownloads = $collection->items->first()->total_downloads ?? 0;
                $totalViews = $collection->items->first()->total_views ?? 0;
                
                return [
                    'id' => $collection->id,
                    'title' => $collection->name,
                    'item_count' => $collection->items_count,
                    'total_downloads' => $totalDownloads,
                    'total_views' => $totalViews,
                    'avg_downloads' => $collection->items_count > 0 ? 
                        round($totalDownloads / $collection->items_count, 1) : 0
                ];
            });
    }

    private function calculateStorageUsage()
    {
        $items = Item::whereNotNull('file_size')->get();
        $totalBytes = 0;
        
        foreach ($items as $item) {
            if (is_numeric($item->file_size)) {
                $totalBytes += $item->file_size;
            } elseif (is_string($item->file_size)) {
                // Convert "2.5 MB" style strings to bytes
                $totalBytes += $this->parseFileSize($item->file_size);
            }
        }
        
        return [
            'bytes' => $totalBytes,
            'human' => $this->formatBytes($totalBytes)
        ];
    }

    private function parseFileSize($size)
    {
        // Simple conversion for demo data
        if (strpos($size, 'MB') !== false) {
            return (float)$size * 1024 * 1024;
        } elseif (strpos($size, 'KB') !== false) {
            return (float)$size * 1024;
        }
        return (int)$size;
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

    private function generateCSVReport($type)
    {
        $handle = fopen('php://output', 'w');
        
        if ($type === 'usage') {
            fputcsv($handle, ['Month', 'Downloads', 'Views']);
            $stats = $this->getUsageStatistics();
            
            foreach ($stats['downloads_by_month'] as $download) {
                $view = $stats['views_by_month']->firstWhere('month', $download->month);
                fputcsv($handle, [
                    $download->month,
                    $download->downloads,
                    $view ? $view->views : 0
                ]);
            }
        } elseif ($type === 'collections') {
            fputcsv($handle, ['Collection', 'Items', 'Total Downloads', 'Total Views', 'Avg Downloads per Item']);
            $stats = $this->getCollectionStats();
            
            foreach ($stats as $collection) {
                fputcsv($handle, [
                    $collection['title'],
                    $collection['item_count'],
                    $collection['total_downloads'],
                    $collection['total_views'],
                    $collection['avg_downloads']
                ]);
            }
        }
        
        fclose($handle);
    }
}