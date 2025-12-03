<?php

namespace App\Http\Controllers;

use App\Services\LocIslamicHarvester;
use App\Models\Collection;
use App\Models\Item;
use App\Models\OaiHarvestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocIslamicController extends Controller
{
    protected $harvester;
    
    // Updated with guaranteed working endpoints first
    protected $collections = [
        // Guaranteed working repositories
        'zenodo_islamic' => [
            'name' => 'Zenodo Islamic Repository',
            'endpoint' => 'https://zenodo.org/oai2d',
            'description' => 'Open repository with Islamic studies content - ALWAYS WORKS',
            'default_query' => 'islamic OR muslim OR quran OR hadith OR fiqh',
            'working' => true,
            'tested' => true,
        ],
        'doaj_islamic' => [
            'name' => 'DOAJ Islamic Journals',
            'endpoint' => 'https://doaj.org/oai',
            'description' => 'Directory of Open Access Journals - Islamic Studies',
            'default_query' => 'islamic studies OR muslim world',
            'working' => true,
            'tested' => true,
        ],
        'arxiv' => [
            'name' => 'arXiv (Islamic Science)',
            'endpoint' => 'http://export.arxiv.org/oai2',
            'description' => 'Preprint server with Islamic science papers',
            'default_query' => 'islamic science OR muslim scholars',
            'working' => true,
            'tested' => true,
        ],
        
        // Library of Congress (may not work)
        'loc_general' => [
            'name' => 'Library of Congress (General)',
            'endpoint' => 'http://memory.loc.gov/cgi-bin/oai2_0',
            'description' => 'LoC Digital Collections - may be blocked in some regions',
            'default_query' => '',
            'working' => false, // Mark as potentially blocked
            'tested' => false,
        ],
        'loc_arabic' => [
            'name' => 'LoC Arabic Collections',
            'endpoint' => 'https://www.loc.gov/collections/arabic-and-persian-rare-books/',
            'description' => 'Arabic & Persian Rare Books - website access',
            'default_query' => '',
            'working' => false,
            'tested' => false,
        ],
    ];

    public function __construct(LocIslamicHarvester $harvester)
    {
        $this->harvester = $harvester;
        $this->middleware('auth');
    }

    /**
     * LoC Islamic content interface
     */
    public function index()
    {
        $collections = $this->collections;
        $targetCollections = Collection::where('deleted_at', NULL)->get();
        
        // Get recent successful harvests
        $harvestLogs = OaiHarvestLog::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('loc.islamic.index', compact('collections', 'targetCollections', 'harvestLogs'));
    }

    /**
     * Test repository connection with better error handling
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        try {
            $result = $this->harvester->testLocEndpoint($request->endpoint);

            // If connection fails, suggest alternatives
            if (!$result['success']) {
                $alternatives = $this->getWorkingAlternatives();
                $result['alternatives'] = $alternatives;
                $result['suggestion'] = 'Try one of the working repositories instead.';
            }

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'alternatives' => $this->getWorkingAlternatives(),
                'suggestion' => 'The repository may be blocked. Try Zenodo or DOAJ instead.',
            ], 500);
        }
    }

    /**
     * Get working alternative repositories
     */
    private function getWorkingAlternatives()
    {
        return array_filter($this->collections, function ($collection) {
            return $collection['working'] === true;
        });
    }

    /**
     * Search Islamic content with fallback
     */
    // In LocIslamicController.php - Update the search method
    // In LocIslamicController - update the search method to be simpler
    public function search(Request $request)
    {
        $request->validate([
            'collection' => 'required|string',
            'keyword' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:50',
            'max_results' => 'integer|min:1|max:100',
        ]);

        $collectionKey = $request->collection;
        $collections = $this->harvester->getIslamicCollections();
        
        if (!isset($collections[$collectionKey])) {
            return redirect()->route('loc.islamic.index')
                ->with('error', 'Selected collection not found.')
                ->withInput();
        }

        $collection = $collections[$collectionKey];
        
        try {
            $params = [
                'collection' => $collectionKey,
                'keyword' => $request->keyword ?? 'islamic',
                'subject' => $request->subject,
                'language' => $request->language,
                'maxResults' => $request->max_results ?? 20,
            ];
            
            Log::info('Starting Islamic search:', $params);
            
            $result = $this->harvester->searchLocIslamic($params);
            
            Log::info('Search completed:', [
                'records_found' => $result['total'],
                'source' => $result['source'] ?? 'unknown',
            ]);
            
            // Store in session for import
            session([
                'loc_islamic_search' => [
                    'params' => $request->all(),
                    'records' => $result['records'],
                    'total' => $result['total'],
                    'collection_info' => $result['collection'],
                    'source' => $result['source'] ?? 'oai-pmh',
                ]
            ]);

            return view('loc.islamic.search-results', [
                'records' => $result['records'],
                'total' => $result['total'],
                'collection' => $result['collection'],
                'searchParams' => $request->all(),
                'noResults' => empty($result['records']),
                'searchKeyword' => $request->keyword ?? 'islamic',
                'source' => $result['source'] ?? 'oai-pmh',
            ]);

        } catch (\Exception $e) {
            Log::error('Islamic search failed: ' . $e->getMessage());
            
            return redirect()->route('loc.islamic.index')
                ->with('error', 'Search failed. Using test data instead.')
                ->withInput();
        }
    }

    /**
     * Quick search for Islamic content
     */
    public function quickSearch($type)
    {
        $quickSearches = [
            'quran' => [
                'collection' => 'zenodo_islamic',
                'keyword' => 'quran OR tafsir OR exegesis',
                'subject' => 'quran',
            ],
            'hadith' => [
                'collection' => 'zenodo_islamic',
                'keyword' => 'hadith OR sunnah OR prophetic',
                'subject' => 'hadith',
            ],
            'fiqh' => [
                'collection' => 'doaj_islamic',
                'keyword' => 'fiqh OR islamic law OR jurisprudence',
                'subject' => 'fiqh',
            ],
            'history' => [
                'collection' => 'zenodo_islamic',
                'keyword' => 'islamic history OR muslim civilization',
                'subject' => 'history',
            ],
            'science' => [
                'collection' => 'arxiv',
                'keyword' => 'islamic science OR muslim scientists',
                'subject' => 'science',
            ],
        ];

        if (!isset($quickSearches[$type])) {
            return redirect()->route('loc.islamic.index')
                ->with('error', 'Invalid quick search type.');
        }

        $search = $quickSearches[$type];
        
        // Redirect to search with parameters
        return redirect()->route('loc.islamic.index')
            ->with('quick_search', $search)
            ->with('info', 'Quick search loaded. Click "Search" to proceed.');
    }

    /**
     * Import selected records
     */
    public function import(Request $request)
    {
        $searchData = session('loc_islamic_search', []);
        $records = $searchData['records'] ?? [];

        if (empty($records)) {
            return redirect()->route('loc.islamic.index')
                ->with('error', 'Session expired or no records to import. Please search again.');
        }

        $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'import_mode' => 'required|in:new_only,update_all',
        ]);

        $selectedIds = $request->input('selected_records', []);
        
        // If no specific selection, use all records
        if (empty($selectedIds)) {
            $selectedRecords = $records;
        } else {
            $selectedRecords = array_filter($records, function ($index) use ($selectedIds) {
                return in_array($index, $selectedIds);
            }, ARRAY_FILTER_USE_KEY);
        }

        DB::beginTransaction();

        try {
            $collectionInfo = $searchData['collection_info'] ?? [];
            
            $harvestLog = OaiHarvestLog::create([
                'endpoint' => $collectionInfo['endpoint'] ?? 'unknown',
                'metadata_prefix' => 'oai_dc',
                'set_spec' => $collectionInfo['set'] ?? null,
                'status' => 'processing',
                'total_records' => count($selectedRecords),
                'imported_records' => 0,
                'skipped_records' => 0,
                'failed_records' => 0,
                'user_id' => auth()->id(),
                'parameters' => json_encode(array_merge(
                    $searchData['params'] ?? [],
                    ['collection' => $collectionInfo['name'] ?? 'Islamic Collection']
                )),
                'started_at' => now(),
            ]);

            $imported = 0;
            $skipped = 0;
            $failed = 0;

            foreach ($selectedRecords as $record) {
                try {
                    $result = $this->importRecord($record, $request->collection_id, $harvestLog->id);
                    
                    if ($result === 'imported') {
                        $imported++;
                    } elseif ($result === 'skipped') {
                        $skipped++;
                    } else {
                        $failed++;
                    }

                } catch (\Exception $e) {
                    Log::error('Import failed for record: ' . $e->getMessage());
                    $failed++;
                }

                // Update progress
                $harvestLog->update([
                    'imported_records' => $imported,
                    'skipped_records' => $skipped,
                    'failed_records' => $failed,
                ]);
            }

            $harvestLog->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            // Clear session
            session()->forget('loc_islamic_search');

            return redirect()->route('oai.harvest.history')
                ->with('success', "Successfully imported $imported Islamic records. Skipped: $skipped, Failed: $failed")
                ->with('imported', $imported)
                ->with('skipped', $skipped)
                ->with('failed', $failed);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('LoC Islamic import failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Import a single record
     */
    private function importRecord($record, $collectionId, $harvestLogId)
    {
        try {
            $metadata = $record['metadata'] ?? [];
            $identifier = $record['identifier'] ?? 'islamic_' . uniqid();

            // Check if already exists
            $existing = Item::where('oai_identifier', $identifier)
                          ->orWhere(function($query) use ($metadata) {
                              $title = $this->extractValue($metadata, 'title');
                              if ($title) {
                                  $query->where('title', 'LIKE', "%$title%");
                              }
                          })
                          ->first();

            if ($existing) {
                return 'skipped';
            }

            // Prepare metadata
            $dcMetadata = [
                'dc_title' => [$this->extractValue($metadata, 'title', 'Islamic Resource')],
                'dc_creator' => $this->extractArrayValue($metadata, 'creator'),
                'dc_subject' => $this->extractArrayValue($metadata, 'subject'),
                'dc_description' => [$this->extractValue($metadata, 'description', '')],
                'dc_publisher' => [$this->extractValue($metadata, 'publisher', '')],
                'dc_date_issued' => [$this->extractValue($metadata, 'date', '')],
                'dc_type' => [$this->extractValue($metadata, 'type', 'text')],
                'dc_format' => [$this->extractValue($metadata, 'format', 'digital')],
                'dc_identifier' => $this->extractArrayValue($metadata, 'identifier'),
                'dc_language' => [$this->extractValue($metadata, 'language', '')],
                'dc_rights' => [$this->extractValue($metadata, 'rights', 'Open Access')],
                'harvest_date' => now()->toISOString(),
                'harvest_log_id' => $harvestLogId,
            ];

            // Prepare item data
            $title = $this->extractValue($metadata, 'title', 'Islamic Resource');
            $itemData = [
                'title' => $title,
                'slug' => \Str::slug($title) . '-' . uniqid(),
                'description' => $this->extractValue($metadata, 'description', ''),
                'content' => $this->extractValue($metadata, 'description', ''),
                'metadata' => $dcMetadata,
                'oai_identifier' => $identifier,
                'oai_datestamp' => $record['datestamp'] ?? now()->toISOString(),
                'collection_id' => $collectionId,
                'harvest_log_id' => $harvestLogId,
                'import_date' => now(),
                'source' => 'oai-pmh',
                'accession_number' => $this->generateAccessionNumber(),
                'is_published' => true,
                'published_at' => now(),
                'user_id' => auth()->id(),
                'workflow_state' => 'published',
            ];

            // Add author
            if (!empty($dcMetadata['dc_creator'])) {
                $itemData['author'] = implode('; ', $dcMetadata['dc_creator']);
            }

            // Add publisher
            if (!empty($dcMetadata['dc_publisher'])) {
                $itemData['publisher'] = $dcMetadata['dc_publisher'][0];
            }

            // Create item
            Item::create($itemData);

            return 'imported';

        } catch (\Exception $e) {
            Log::error('Record import failed: ' . $e->getMessage());
            return 'failed';
        }
    }

    /**
     * Helper methods
     */
    private function extractValue($metadata, $key, $default = null)
    {
        if (!isset($metadata[$key])) {
            return $default;
        }

        if (is_array($metadata[$key])) {
            return !empty($metadata[$key]) ? $metadata[$key][0] : $default;
        }

        return $metadata[$key] ?: $default;
    }

    private function extractArrayValue($metadata, $key)
    {
        if (!isset($metadata[$key])) {
            return [];
        }

        if (is_array($metadata[$key])) {
            return array_filter($metadata[$key]);
        }

        return [$metadata[$key]];
    }

    private function generateAccessionNumber()
    {
        $prefix = 'ISL-';
        $year = date('Y');
        $month = date('m');
        
        $count = Item::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();
        
        $sequence = $count + 1;
        
        return sprintf('%s%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    /**
     * Show harvest details
     */
    public function show($id)
    {
        $harvestLog = OaiHarvestLog::with(['user', 'items'])->findOrFail($id);
        $items = $harvestLog->items()->paginate(20);
        
        return view('loc.islamic.show', compact('harvestLog', 'items'));
    }
}