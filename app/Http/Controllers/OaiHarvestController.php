<?php

namespace App\Http\Controllers;

use App\Services\OaiPmhHarvester;
use App\Models\Item;
use App\Models\Collection;
use App\Models\OaiHarvestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OaiHarvestController extends Controller
{
    protected $harvester;
    protected $popularRepositories = [
        'http://export.arxiv.org/oai2' => 'arXiv.org e-Print Archive',
        'https://zenodo.org/oai2d' => 'Zenodo Research Repository',
        'https://pubmed.ncbi.nlm.nih.gov/oai/oai.cgi' => 'PubMed Central',
        'https://doaj.org/oai' => 'Directory of Open Access Journals',
        'https://digital.library.unt.edu/explore/collections/UNTETD/oai/' => 'UNT Digital Library',
        'https://dspace.mit.edu/oai/request' => 'MIT DSpace',
        'http://eprints.soton.ac.uk/cgi/oai2' => 'University of Southampton ePrints',
        'https://www.islamicmanuscripts.info/oai' => 'Islamic Manuscripts (Princeton)',
    'https://dspace.cam.ac.uk/oai/request' => 'University of Cambridge Islamic Collections',
    'https://dspace.mit.edu/oai/request' => 'MIT Aga Khan Documentation Center',
    'https://edoc.hu-berlin.de/oai' => 'Humboldt University Islamic Studies',
    'https://ir.lib.uwo.ca/do/oai/' => 'Western University Islamic Studies',
    'https://scholarworks.gsu.edu/do/oai/' => 'Georgia State University Middle East Collections',
    'http://eprints.soas.ac.uk/cgi/oai2' => 'SOAS University of London Middle East Collections',
    'https://core.ac.uk/oai' => 'CORE - Includes Islamic Studies content',
    'https://www.doaj.org/oai' => 'DOAJ - Islamic Studies Journals',
    
    // Specialized Islamic Repositories
    'http://www.alukah.net/oai/' => 'Alukah Islamic Digital Library',
    'https://waqfeya.com/oai' => 'Waqfeya Islamic Books',
    'http://shamela.ws/oai' => 'Shamela Islamic Library',
    'https://www.hadithportal.com/oai' => 'Hadith Portal',
    'http://qurancomplex.org/oai' => 'King Fahd Quran Complex',
    
    // University Repositories with Islamic Content
    'https://etheses.dur.ac.uk/oai' => 'Durham University Islamic Theses',
    'https://etd.ohiolink.edu/oai' => 'OhioLINK ETD - Islamic Studies',
    'https://open.bu.edu/oai' => 'Boston University Islamic Collections',
    'https://scholarworks.umass.edu/oai' => 'UMass Islamic Studies',
    
    // Test Repositories (Always Available)
    'http://memory.loc.gov/cgi-bin/oai2_0' => 'Library of Congress (Test)',
    'https://zenodo.org/oai2d' => 'Zenodo (Test - Multidisciplinary)',
    ];

    public function __construct(OaiPmhHarvester $harvester)
    {
        $this->harvester = $harvester;
        $this->middleware('auth');
    }

    /**
     * Show harvester interface
     */
    public function index()
    {
        $harvestLogs = OaiHarvestLog::latest()->take(10)->get();
        $collections = Collection::get();
        
        return view('oai.harvest.index', [
            'popularRepositories' => $this->popularRepositories,
            'harvestLogs' => $harvestLogs,
            'collections' => $collections,
        ]);
    }

    /**
     * Test connection to OAI-PMH endpoint
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        try {
            $this->harvester->setEndpoint($request->endpoint);
            $identify = $this->harvester->identify();
            
            return response()->json([
                'success' => true,
                'data' => $identify,
                'message' => 'Successfully connected to repository'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get metadata formats from endpoint
     */
    public function getMetadataFormats(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        try {
            $this->harvester->setEndpoint($request->endpoint);
            $formats = $this->harvester->listMetadataFormats();
            
            return response()->json([
                'success' => true,
                'data' => $formats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sets from endpoint
     */
    public function getSets(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        try {
            $this->harvester->setEndpoint($request->endpoint);
            $sets = $this->harvester->listSets();
            
            return response()->json([
                'success' => true,
                'data' => $sets,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview records before harvesting
     */
    public function preview(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'metadata_prefix' => 'required|string',
            'set' => 'nullable|string',
            'from_date' => 'nullable|date',
            'until_date' => 'nullable|date',
            'limit' => 'integer|min:1|max:50',
        ]);
        // dd($request);
        try {
            $this->harvester->setEndpoint($request->endpoint);
            
            $params = [
                'metadataPrefix' => $request->metadata_prefix,
                'set' => $request->set,
                'from' => $request->from_date,
                'until' => $request->until_date,
                'language' => $request->language
            ];

            $result = $this->harvester->listRecords($params);
            $records = array_slice($result['records'], 0, $request->limit ?? 10);

            // Store preview data in session
            session([
                'harvest_preview' => [
                    'endpoint' => $request->endpoint,
                    'metadata_prefix' => $request->metadata_prefix,
                    'set' => $request->set,
                    'from_date' => $request->from_date,
                    'until_date' => $request->until_date,
                    'records' => $records,
                ]
            ]);

            return view('oai.harvest.preview', [
                'records' => $records,
                'params' => $request->all(),
                'totalAvailable' => $result['completeListSize'] ?? count($result['records']),
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to preview records: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Harvest and import records
     */
    public function harvest(Request $request)
    {
        $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'import_mode' => 'required|in:preview,full,resume',
            'batch_size' => 'integer|min:1|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $previewData = session('harvest_preview');
            
            if (!$previewData && $request->import_mode !== 'resume') {
                throw new \Exception('No preview data found. Please preview first.');
            }

            $harvestLog = OaiHarvestLog::create([
                'endpoint' => $previewData['endpoint'] ?? 'Resumed harvest',
                'metadata_prefix' => $previewData['metadata_prefix'] ?? 'oai_dc',
                'set_spec' => $previewData['set'] ?? null,
                'from_date' => $previewData['from_date'] ?? null,
                'until_date' => $previewData['until_date'] ?? null,
                'status' => 'processing',
                'total_records' => 0,
                'imported_records' => 0,
                'skipped_records' => 0,
                'failed_records' => 0,
                'user_id' => auth()->id(),
                'parameters' => json_encode($request->all()),
            ]);

            $imported = 0;
            $skipped = 0;
            $failed = 0;
            $batchSize = $request->batch_size ?? 50;

            if ($request->import_mode === 'preview') {
                // Import only previewed records
                $records = $previewData['records'] ?? [];
                
                foreach ($records as $record) {
                    $result = $this->importRecord($record, $request->collection_id, $harvestLog->id);
                    
                    if ($result === 'imported') $imported++;
                    elseif ($result === 'skipped') $skipped++;
                    else $failed++;
                }
            } else {
                // Full harvest with resumption token
                $this->harvester->setEndpoint($previewData['endpoint']);
                
                $params = [
                    'metadataPrefix' => $previewData['metadata_prefix'],
                    'set' => $previewData['set'],
                    'from' => $previewData['from_date'],
                    'until' => $previewData['until_date'],
                ];

                $hasMore = true;
                $totalProcessed = 0;

                while ($hasMore && $totalProcessed < $batchSize) {
                    $result = $this->harvester->listRecords($params);
                    
                    foreach ($result['records'] as $record) {
                        if ($totalProcessed >= $batchSize) break;
                        
                        $importResult = $this->importRecord($record, $request->collection_id, $harvestLog->id);
                        
                        if ($importResult === 'imported') $imported++;
                        elseif ($importResult === 'skipped') $skipped++;
                        else $failed++;
                        
                        $totalProcessed++;
                    }

                    // Check for resumption token
                    if (!empty($result['resumptionToken'])) {
                        $params = ['resumptionToken' => $result['resumptionToken']];
                        $harvestLog->update(['resumption_token' => $result['resumptionToken']]);
                    } else {
                        $hasMore = false;
                    }

                    // Update progress
                    $harvestLog->update([
                        'total_records' => $totalProcessed,
                        'imported_records' => $imported,
                        'skipped_records' => $skipped,
                        'failed_records' => $failed,
                    ]);
                }
            }

            $harvestLog->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            // Clear session data
            session()->forget('harvest_preview');

            return redirect()->route('oai.harvest.index')
                ->with('success', "Harvest completed! Imported: $imported, Skipped: $skipped, Failed: $failed");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Harvest failed: ' . $e->getMessage());

            if (isset($harvestLog)) {
                $harvestLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return redirect()->back()
                ->with('error', 'Harvest failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Update importRecord method with more debugging
    public function importRecord($record, $collectionId, $harvestLogId)
    {
        \Log::info('=== IMPORT RECORD START ===');
        \Log::info('Record data:', $record);
        
        try {
            $metadata = $record['metadata'] ?? [];
            \Log::info('Raw metadata:', $metadata);
            
            // Transform OAI metadata to match your DC format
            $transformedMetadata = [
                'dc_title' => [$this->extractValue($metadata, 'title', 'Untitled Resource')],
                'dc_creator' => $this->extractArrayValue($metadata, 'creator'),
                'dc_subject' => $this->extractArrayValue($metadata, 'subject'),
                'dc_description' => [$this->extractValue($metadata, 'description', '')],
                'dc_publisher' => [$this->extractValue($metadata, 'publisher', '')],
                'dc_date_issued' => [$this->extractValue($metadata, 'date', '')],
                'dc_type' => [$this->extractValue($metadata, 'type', 'text')],
                'dc_format' => [$this->extractValue($metadata, 'format', 'digital')],
                'dc_identifier' => $this->extractArrayValue($metadata, 'identifier'),
                'dc_language' => [$this->extractValue($metadata, 'language', 'en')],
                'dc_rights' => [$this->extractValue($metadata, 'rights', '')],
                
                // OAI-specific metadata
                'oai_identifier' => $record['identifier'] ?? null,
                'oai_datestamp' => $record['datestamp'] ?? null,
                'oai_set_spec' => $record['setSpec'] ?? null,
                'harvest_date' => now()->toISOString(),
                'harvest_log_id' => $harvestLogId,
            ];
            // dd($transformedMetadata);
            \Log::info('Transformed metadata:', $transformedMetadata);

            // Check if item already exists
            $oaiIdentifier = $record['identifier'] ?? null;
            \Log::info('Checking for existing item with OAI identifier:', ['identifier' => $oaiIdentifier]);
            
            $existing = null;
            
            if ($oaiIdentifier) {
                $existing = Item::where('oai_identifier', $oaiIdentifier)->first();
            }
            
            // Also check by DC identifiers
            if (!$existing && !empty($transformedMetadata['dc_identifier'])) {
                foreach ($transformedMetadata['dc_identifier'] as $identifier) {
                    $existing = Item::whereJsonContains('metadata->dc_identifier', $identifier)->first();
                    if ($existing) break;
                }
            }

            if ($existing) {
                \Log::info('Existing item found:', ['id' => $existing->id]);
                
                // Update existing record
                $existingMetadata = $existing->metadata ?? [];
                $mergedMetadata = $this->mergeMetadata($existingMetadata, $transformedMetadata);
                
                $existing->update([
                    'metadata' => $mergedMetadata,
                    'oai_datestamp' => $record['datestamp'] ?? null,
                    'import_date' => now(),
                    'harvest_log_id' => $harvestLogId,
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                
                \Log::info('Updated existing item');
                return 'updated';
            }

            \Log::info('Creating new item...');
            
            // Prepare item data matching your store format
            $title = $this->extractValue($metadata, 'title', 'Untitled Resource');
            $itemData = [
                'title' => $title,
                'slug' => \Str::slug($title) . '-' . uniqid(),
                'description' => $this->extractValue($metadata, 'description', ''),
                'content' => $this->extractValue($metadata, 'description', ''),
                'metadata' => $transformedMetadata,
                'oai_identifier' => $record['identifier'] ?? null,
                'oai_datestamp' => $record['datestamp'] ?? null,
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

            \Log::info('Item data prepared:', $itemData);

            // Extract creator for author field if exists
            // dd($itemData);

            // Create the item
            $item = Item::create($itemData);
            \Log::info('Item created successfully:', ['id' => $item->id]);

            return 'imported';

        } catch (\Exception $e) {
            \Log::error('Failed to import record: ' . $e->getMessage());
            \Log::error('Record data: ' . json_encode($record));
            \Log::error('Trace: ' . $e->getTraceAsString());
            return 'failed';
        }
    }

    /**
     * Extract single value from OAI metadata
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

    /**
     * Extract array value from OAI metadata (for dc_creator, dc_subject, etc.)
     */
    private function extractArrayValue($metadata, $key)
    {
        if (!isset($metadata[$key])) {
            return [];
        }

        if (is_array($metadata[$key])) {
            return array_filter($metadata[$key]); // Remove empty values
        }

        return [$metadata[$key]];
    }

/**
 * Merge metadata arrays while preserving existing data
 */
private function mergeMetadata($existing, $new)
{
    $merged = $existing;
    
    foreach ($new as $key => $value) {
        if (isset($merged[$key]) && is_array($merged[$key]) && is_array($value)) {
            // For arrays like dc_creator, dc_subject - merge unique values
            $merged[$key] = array_unique(array_merge($merged[$key], $value));
        } else {
            $merged[$key] = $value;
        }
    }
    
    return $merged;
}

    /**
     * Generate accession number
     */
    private function generateAccessionNumber()
    {
        $prefix = 'OAI-';
        $year = date('Y');
        $month = date('m');
        $sequence = Item::whereYear('created_at', $year)
                       ->whereMonth('created_at', $month)
                       ->count() + 1;

        return sprintf('%s%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    // In OaiHarvestController.php - update history method
    public function history(Request $request)
    {
        $query = OaiHarvestLog::withCount('items');
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $harvestLogs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate statistics
        $totalHarvests = OaiHarvestLog::count();
        $successfulHarvests = OaiHarvestLog::where('status', 'completed')->count();
        $totalImported = OaiHarvestLog::sum('imported_records');
        
        $successRate = $totalHarvests > 0 ? ($successfulHarvests / $totalHarvests) * 100 : 0;
        
        return view('oai.harvest.history', compact(
            'harvestLogs',
            'totalHarvests',
            'successfulHarvests',
            'totalImported',
            'successRate'
        ));
    }

    /**
     * View harvest details
     */
    // In OaiHarvestController.php - update showHarvest method
    public function showHarvest($id)
    {
        $harvestLog = OaiHarvestLog::with(['user', 'items'])->findOrFail($id);
        $items = $harvestLog->items()->latest()->paginate(10);
        
        return view('oai.harvest.show', compact('harvestLog', 'items'));
    }

    /**
     * Resume a harvest
     */
    public function resume($id)
    {
        $harvestLog = OaiHarvestLog::findOrFail($id);
        
        if (!$harvestLog->resumption_token) {
            return redirect()->back()->with('error', 'No resumption token available for this harvest');
        }

        // Store resume data in session
        session([
            'harvest_preview' => [
                'endpoint' => $harvestLog->endpoint,
                'metadata_prefix' => $harvestLog->metadata_prefix,
                'set' => $harvestLog->set_spec,
                'from_date' => $harvestLog->from_date,
                'until_date' => $harvestLog->until_date,
                'resumption_token' => $harvestLog->resumption_token,
            ]
        ]);

        return redirect()->route('oai.harvest.index')
            ->with('info', 'Harvest resumed. Please configure and start the harvest.');
    }


        // app/Http/Controllers/OaiHarvestController.php - Add new methods
    /**
     * Show search interface
     */
    public function search()
    {
        $collections = Collection::where('deleted_at', NULL)->get();
        $popularRepositories = $this->popularRepositories;
        
        return view('oai.harvest.search', compact('collections', 'popularRepositories'));
    }

    /**
     * Perform search on OAI-PMH repository
     */
    public function performSearch(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keyword' => 'nullable|string|max:255',
            'metadata_prefix' => 'required|string',
            'set' => 'nullable|string',
            'from_date' => 'nullable|date',
            'until_date' => 'nullable|date',
            'max_results' => 'integer|min:1|max:200',
        ]);

        try {
            $this->harvester->setEndpoint($request->endpoint);
            
            $searchParams = [
                'metadataPrefix' => $request->metadata_prefix,
                'set' => $request->set,
                'from' => $request->from_date,
                'until' => $request->until_date,
                'keyword' => $request->keyword,
                'language' => $request->language,
                'publisher' => $request->publisher,
                'maxResults' => $request->max_results ?? 50,
            ];

            $result = $this->harvester->searchRecords($searchParams);
            
            // Store search results in session for selective harvesting
            session([
                'search_results' => [
                    'endpoint' => $request->endpoint,
                    'metadata_prefix' => $request->metadata_prefix,
                    'params' => $request->all(),
                    'records' => $result['records'],
                    'selected_records' => [], // Will store selected identifiers
                ]
            ]);

            return view('oai.harvest.search-results', [
                'records' => $result['records'],
                'total' => $result['total'],
                'hasMore' => $result['hasMore'],
                'keyword' => $request->keyword,
                'language' => $request->language,
                'publisher' => $request->publisher,
                'endpoint' => $request->endpoint,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('oai.harvest.search')
                ->with('error', 'Search failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Select records for harvesting
     */
    public function selectRecords(Request $request)
    {
        $searchResults = session('search_results', []);
        
        $collections = Collection::get();
        if (empty($searchResults['records'])) {
            return redirect()->route('oai.harvest.search')
                ->with('error', 'No search results found. Please search first.');
        }

        $selectedIds = $request->input('selected_records', []);
        $selectedRecords = [];
        
        // Filter only selected records
        foreach ($searchResults['records'] as $record) {
            if (in_array($record['identifier'], $selectedIds)) {
                $selectedRecords[] = $record;
            }
        }
        
        // Update session with selected records
        $searchResults['selected_records'] = $selectedRecords;
        session(['search_results' => $searchResults]);
        
        return view('oai.harvest.selected-preview', [
            'collections' => $collections,
            'selectedRecords' => $selectedRecords,
            'totalSelected' => count($selectedRecords),
        ]);
    }

    /**
     * Harvest selected records only
     */
    // In OaiHarvestController.php - update harvestSelected method with debugging
    public function harvestSelected(Request $request)
    {
        \Log::info('=== HARVEST SELECTED START ===');
        
        // Debug session data
        \Log::info('All session data:', session()->all());
        
        $searchResults = session('search_results', []);
        \Log::info('Search results from session:', $searchResults);
        
        // Check if we have the right structure
        if (empty($searchResults) || !isset($searchResults['selected_records'])) {
            \Log::error('Invalid session structure or missing selected_records');
            \Log::error('Session keys: ' . json_encode(array_keys($searchResults)));
            
            // Try to get from old session key
            $searchResults = session('harvest_preview', []);
            \Log::info('Trying harvest_preview session:', $searchResults);
            
            if (empty($searchResults)) {
                return redirect()->route('oai.harvest.search')
                    ->with('error', 'Session expired or no search results found. Please search again.');
            }
        }

        $selectedRecords = $searchResults['selected_records'] ?? [];
        \Log::info('Selected records count:', ['count' => count($selectedRecords)]);
        
        if (empty($selectedRecords)) {
            return redirect()->route('oai.harvest.search')
                ->with('error', 'No records selected for harvesting.');
        }

        $request->validate([
            'collection_id' => 'required|exists:collections,id',
        ]);

        DB::beginTransaction();

        try {
            \Log::info('Creating harvest log...');
            
            $harvestLog = OaiHarvestLog::create([
                'endpoint' => $searchResults['endpoint'] ?? 'Unknown',
                'metadata_prefix' => $searchResults['metadata_prefix'] ?? 'oai_dc',
                'set_spec' => $searchResults['set'] ?? $searchResults['params']['set'] ?? null,
                'from_date' => $searchResults['from_date'] ?? $searchResults['params']['from_date'] ?? null,
                'until_date' => $searchResults['until_date'] ?? $searchResults['params']['until_date'] ?? null,
                'status' => 'processing',
                'total_records' => count($selectedRecords),
                'imported_records' => 0,
                'skipped_records' => 0,
                'failed_records' => 0,
                'user_id' => auth()->id(),
                'parameters' => json_encode($searchResults['params'] ?? []),
                'started_at' => now(),
            ]);

            \Log::info('Harvest log created:', ['id' => $harvestLog->id]);

            $imported = 0;
            $skipped = 0;
            $failed = 0;

            foreach ($selectedRecords as $index => $record) {
                \Log::info("Processing record {$index}:", ['identifier' => $record['identifier'] ?? 'Unknown']);
                
                try {
                    $result = $this->importRecord($record, $request->collection_id, $harvestLog->id);
                    \Log::info("Record {$index} result:", ['result' => $result]);
                    
                    if ($result === 'imported' || $result === 'updated') {
                        $imported++;
                    } elseif ($result === 'skipped') {
                        $skipped++;
                    } else {
                        $failed++;
                    }
                    
                } catch (\Exception $e) {
                    \Log::error("Error processing record {$index}: " . $e->getMessage());
                    $failed++;
                }
                
                // Update progress periodically
                if ($index % 5 === 0) {
                    $harvestLog->update([
                        'imported_records' => $imported,
                        'skipped_records' => $skipped,
                        'failed_records' => $failed,
                    ]);
                }
            }

            // Final update
            $harvestLog->update([
                'imported_records' => $imported,
                'skipped_records' => $skipped,
                'failed_records' => $failed,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            \Log::info('Harvest completed:', [
                'imported' => $imported,
                'skipped' => $skipped,
                'failed' => $failed,
                'total' => count($selectedRecords)
            ]);

            // Clear session data
            session()->forget(['search_results', 'harvest_preview']);

            return redirect()->route('oai.harvest.history')
                ->with('success', "Successfully harvested $imported selected records. Skipped: $skipped, Failed: $failed");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Selective harvest failed: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Harvest failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}