<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OaiPmhHarvester
{
    protected $client;
    protected $endpoint;
    protected $timeout = 30;
    protected $cacheTime = 3600; // 1 hour cache
    protected $maxConcurrentRequests = 5; // For parallel requests

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => $this->timeout,
            'headers' => [
                'User-Agent' => 'DCMS-OAI-Harvester/1.0',
                'Accept' => 'application/xml',
            ],
            'verify' => false, // Set to true in production
            'connection_timeout' => 10,
        ]);
    }

    /**
     * Set the OAI-PMH endpoint URL
     */
    public function setEndpoint($url)
    {
        $this->endpoint = $url;
        return $this;
    }

    /**
     * Test connection to OAI-PMH endpoint
     */
    public function identify()
    {
        $cacheKey = 'oai_identify_' . md5($this->endpoint);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $response = $this->makeRequest(['verb' => 'Identify']);
            return $this->parseIdentify($response);
        });
    }

    /**
     * Get available metadata formats
     */
    public function listMetadataFormats($identifier = null)
    {
        $cacheKey = 'oai_formats_' . md5($this->endpoint . ($identifier ?? ''));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($identifier) {
            $params = ['verb' => 'ListMetadataFormats'];
            if ($identifier) {
                $params['identifier'] = $identifier;
            }

            $response = $this->makeRequest($params);
            return $this->parseMetadataFormats($response);
        });
    }

    /**
     * Get available sets
     */
    public function listSets()
    {
        $cacheKey = 'oai_sets_' . md5($this->endpoint);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $response = $this->makeRequest(['verb' => 'ListSets']);
            return $this->parseSets($response);
        });
    }

    /**
     * Harvest records
     */
    public function listRecords($params = [])
    {
        $defaultParams = [
            'verb' => 'ListRecords',
            'metadataPrefix' => 'oai_dc',
            'from' => null,
            'until' => null,
            'set' => null,
            'resumptionToken' => null,
        ];

        $params = array_merge($defaultParams, $params);
        
        // Remove null parameters
        $params = array_filter($params, function ($value) {
            return $value !== null;
        });

        $cacheKey = 'oai_records_' . md5($this->endpoint . json_encode($params));
        
        return Cache::remember($cacheKey, 600, function () use ($params) { // Cache for 10 minutes
            $response = $this->makeRequest($params);
            return $this->parseListRecords($response);
        });
    }

    /**
     * Get a single record
     */
    public function getRecord($identifier, $metadataPrefix = 'oai_dc')
    {
        $cacheKey = 'oai_record_' . md5($this->endpoint . $identifier . $metadataPrefix);
        
        return Cache::remember($cacheKey, 3600, function () use ($identifier, $metadataPrefix) {
            $params = [
                'verb' => 'GetRecord',
                'identifier' => $identifier,
                'metadataPrefix' => $metadataPrefix,
            ];

            $response = $this->makeRequest($params);
            return $this->parseRecord($response);
        });
    }

    /**
     * Make HTTP request to OAI-PMH endpoint
     */
    private function makeRequest($params)
    {
        try {
            $response = $this->client->get($this->endpoint, [
                'query' => $params,
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode !== 200) {
                throw new \Exception("HTTP Error: $statusCode");
            }

            // Load XML
            $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml === false) {
                throw new \Exception('Invalid XML response');
            }

            // Check for OAI-PMH errors
            if (isset($xml->error)) {
                $errorCode = (string) $xml->error['code'];
                $errorMsg = (string) $xml->error;
                throw new \Exception("OAI-PMH Error ($errorCode): $errorMsg");
            }

            // Register namespaces
            $xml->registerXPathNamespace('oai', 'http://www.openarchives.org/OAI/2.0/');
            $xml->registerXPathNamespace('oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
            $xml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
            $xml->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');

            return $xml;

        } catch (RequestException $e) {
            Log::error('OAI-PMH Request failed: ' . $e->getMessage());
            throw new \Exception('Connection to OAI-PMH endpoint failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('OAI-PMH Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse Identify response
     */
    private function parseIdentify($xml)
    {
        $identify = $xml->Identify;
        
        return [
            'repositoryName' => (string) $identify->repositoryName,
            'baseURL' => (string) $identify->baseURL,
            'protocolVersion' => (string) $identify->protocolVersion,
            'adminEmail' => (string) $identify->adminEmail,
            'earliestDatestamp' => (string) $identify->earliestDatestamp,
            'deletedRecord' => (string) $identify->deletedRecord,
            'granularity' => (string) $identify->granularity,
            'description' => $this->parseDescriptions($identify),
        ];
    }

    /**
     * Parse metadata formats
     */
    private function parseMetadataFormats($xml)
    {
        $formats = [];
        
        foreach ($xml->ListMetadataFormats->metadataFormat as $format) {
            $formats[] = [
                'metadataPrefix' => (string) $format->metadataPrefix,
                'schema' => (string) $format->schema,
                'metadataNamespace' => (string) $format->metadataNamespace,
            ];
        }
        
        return $formats;
    }

    /**
     * Parse sets
     */
    private function parseSets($xml)
    {
        $sets = [];
        
        foreach ($xml->ListSets->set as $set) {
            $sets[] = [
                'setSpec' => (string) $set->setSpec,
                'setName' => (string) $set->setName,
                'setDescription' => isset($set->setDescription) ? (string) $set->setDescription : null,
            ];
        }
        
        return $sets;
    }

    /**
     * Parse ListRecords response
     */
    private function parseListRecords($xml)
    {
        $result = [
            'records' => [],
            'resumptionToken' => null,
            'cursor' => null,
            'completeListSize' => null,
        ];

        // Parse records
        foreach ($xml->ListRecords->record as $record) {
            $result['records'][] = $this->parseSingleRecord($record);
        }

        // Parse resumption token if exists
        if (isset($xml->ListRecords->resumptionToken)) {
            $resumptionToken = $xml->ListRecords->resumptionToken;
            $result['resumptionToken'] = (string) $resumptionToken;
            $result['cursor'] = isset($resumptionToken['cursor']) ? (int) $resumptionToken['cursor'] : null;
            $result['completeListSize'] = isset($resumptionToken['completeListSize']) ? (int) $resumptionToken['completeListSize'] : null;
        }

        return $result;
    }

    /**
     * Parse a single record
     */
    private function parseSingleRecord($record)
    {
        $header = $record->header;
        $metadata = isset($record->metadata) ? $this->parseMetadata($record->metadata) : null;

        return [
            'identifier' => (string) $header->identifier,
            'datestamp' => (string) $header->datestamp,
            'setSpec' => isset($header->setSpec) ? (string) $header->setSpec : null,
            'status' => isset($header['status']) ? (string) $header['status'] : null,
            'metadata' => $metadata,
            'raw' => $record->asXML(),
        ];
    }

    /**
     * Parse metadata based on format
     */
    private function parseMetadata($metadata)
    {
        $parsed = [];

        // Check for Dublin Core
        $dc = $metadata->children('http://www.openarchives.org/OAI/2.0/oai_dc/')
                      ->children('http://purl.org/dc/elements/1.1/');

        if ($dc->count() > 0) {
            $parsed = $this->parseDublinCore($dc);
            $parsed['format'] = 'oai_dc';
        }

        // Check for MODS
        $mods = $metadata->children('http://www.loc.gov/mods/v3');
        if ($mods->count() > 0) {
            $parsed = $this->parseMods($mods);
            $parsed['format'] = 'mods';
        }

        return $parsed;
    }

    private function parseDublinCore($dc)
    {
        $result = [];

        // Map DC fields to your format
        $fieldMappings = [
            'title' => 'title',
            'creator' => 'creator',
            'subject' => 'subject',
            'description' => 'description',
            'publisher' => 'publisher',
            'date' => 'date',
            'type' => 'type',
            'format' => 'format',
            'identifier' => 'identifier',
            'source' => 'source',
            'language' => 'language',
            'relation' => 'relation',
            'coverage' => 'coverage',
            'rights' => 'rights',
        ];

        foreach ($fieldMappings as $dcField => $resultField) {
            if (isset($dc->{$dcField})) {
                $values = [];
                foreach ($dc->{$dcField} as $element) {
                    $value = trim((string) $element);
                    if (!empty($value)) {
                        $values[] = $value;
                    }
                }
                
                if (!empty($values)) {
                    $result[$resultField] = $values;
                }
            }
        }

        return $result;
    }

    private function parseMods($mods)
    {
        $result = [];

        // Title
        if (isset($mods->titleInfo->title)) {
            $result['title'] = [trim((string) $mods->titleInfo->title)];
        }

        // Creator
        if (isset($mods->name)) {
            $creators = [];
            foreach ($mods->name as $name) {
                if (isset($name->namePart)) {
                    $creator = trim((string) $name->namePart);
                    if (!empty($creator)) {
                        $creators[] = $creator;
                    }
                }
            }
            if (!empty($creators)) {
                $result['creator'] = $creators;
            }
        }

        // Subject
        if (isset($mods->subject)) {
            $subjects = [];
            foreach ($mods->subject as $subject) {
                if (isset($subject->topic)) {
                    $subjectText = trim((string) $subject->topic);
                    if (!empty($subjectText)) {
                        $subjects[] = $subjectText;
                    }
                }
            }
            if (!empty($subjects)) {
                $result['subject'] = $subjects;
            }
        }

        // Description/Abstract
        if (isset($mods->abstract)) {
            $result['description'] = [trim((string) $mods->abstract)];
        }

        // Publisher
        if (isset($mods->originInfo->publisher)) {
            $result['publisher'] = [trim((string) $mods->originInfo->publisher)];
        }

        // Date
        if (isset($mods->originInfo->dateIssued)) {
            $result['date'] = [trim((string) $mods->originInfo->dateIssued)];
        }

        // Type
        if (isset($mods->genre)) {
            $result['type'] = [trim((string) $mods->genre)];
        }

        // Identifiers
        if (isset($mods->identifier)) {
            $identifiers = [];
            foreach ($mods->identifier as $identifier) {
                $id = trim((string) $identifier);
                if (!empty($id)) {
                    $identifiers[] = $id;
                }
            }
            if (!empty($identifiers)) {
                $result['identifier'] = $identifiers;
            }
        }

        return $result;
    }

    /**
     * Parse descriptions from Identify
     */
    private function parseDescriptions($identify)
    {
        $descriptions = [];
        
        if (isset($identify->description)) {
            foreach ($identify->description as $desc) {
                $descriptions[] = $desc->asXML();
            }
        }
        
        return $descriptions;
    }

    /**
     * Optimized search with caching and batch processing
     */
    public function searchRecords($params = [])
    {
        $defaultParams = [
            'verb' => 'ListRecords',
            'metadataPrefix' => 'oai_dc',
            'set' => null,
            'from' => null,
            'until' => null,
            'keyword' => null,
            'maxResults' => 50,
            'cacheResults' => true,
            'useStreaming' => false,
            'language' => null
        ];

        $params = array_merge($defaultParams, $params);
        
        // Use streaming for large result sets
        if ($params['useStreaming'] || $params['maxResults'] > 100) {
            return $this->searchStreaming($params);
        }
        
        // Generate cache key
        $cacheKey = 'oai_search_' . md5(json_encode($params) . $this->endpoint);
        
        if ($params['cacheResults']) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }
        
        $searchParams = [
            'metadataPrefix' => $params['metadataPrefix'],
            'set' => $params['set'],
            'from' => $params['from'],
            'until' => $params['until'],
        ];

        $searchParams = array_filter($searchParams, function ($value) {
            return $value !== null;
        });

        $allRecords = [];
        $hasMore = true;
        $processed = 0;
        $batchSize = 100;

        while ($hasMore && $processed < $params['maxResults']) {
            try {
                $response = $this->makeRequest(array_merge(['verb' => 'ListRecords'], $searchParams));
                $records = $this->parseListRecords($response);
                
                // Process in batches for better memory management
                $recordChunks = array_chunk($records['records'], $batchSize);
                
                foreach ($recordChunks as $chunk) {
                    if (!empty($params['keyword'])) {
                        $filteredRecords = $this->filterRecordsByKeywordOptimized($chunk, $params['keyword']);
                        $allRecords = array_merge($allRecords, $filteredRecords);
                    } else {
                        $allRecords = array_merge($allRecords, $chunk);
                    }
                    
                    $processed = count($allRecords);
                    
                    if ($processed >= $params['maxResults']) {
                        $allRecords = array_slice($allRecords, 0, $params['maxResults']);
                        $hasMore = false;
                        break;
                    }
                }

                // Check resumption token
                if (!empty($records['resumptionToken']) && $processed < $params['maxResults']) {
                    $searchParams = ['resumptionToken' => $records['resumptionToken']];
                    
                    // Cache resumption token for future use
                    if ($params['cacheResults']) {
                        Cache::put('oai_resumption_' . md5($records['resumptionToken']), 
                            ['params' => $searchParams, 'cursor' => $records['cursor']], 
                            3600);
                    }
                } else {
                    $hasMore = false;
                }

            } catch (\Exception $e) {
                Log::error('Search failed: ' . $e->getMessage());
                $hasMore = false;
            }
        }

        $result = [
            'records' => $allRecords,
            'total' => count($allRecords),
            'hasMore' => $hasMore,
        ];
        
        // Cache results
        if ($params['cacheResults']) {
            Cache::put($cacheKey, $result, 300); // Cache for 5 minutes
        }
        
        return $result;
    }

    /**
     * Optimized keyword filtering with early exit
     */
    private function filterRecordsByKeywordOptimized($records, $keyword)
    {
        $keyword = strtolower(trim($keyword));
        $keywords = preg_split('/\s+/', $keyword);
        
        return array_filter($records, function ($record) use ($keywords) {
            $metadata = $record['metadata'] ?? [];
            $searchText = '';
            
            // Concatenate all searchable fields once
            $searchableFields = ['title', 'description', 'subject', 'creator'];
            foreach ($searchableFields as $field) {
                if (isset($metadata[$field])) {
                    $searchText .= ' ' . (is_array($metadata[$field]) 
                        ? implode(' ', $metadata[$field]) 
                        : (string) $metadata[$field]);
                }
            }
            
            $searchText = strtolower($searchText);
            
            // Check all keywords
            foreach ($keywords as $kw) {
                if (strpos($searchText, $kw) === false) {
                    return false;
                }
            }
            
            return true;
        });
    }

    /**
     * Get multiple records in parallel (faster for bulk operations)
     */
    public function getRecordsByIdentifiersParallel($identifiers, $metadataPrefix = 'oai_dc')
    {
        $cacheKey = 'oai_bulk_' . md5($this->endpoint . json_encode($identifiers) . $metadataPrefix);
        
        return Cache::remember($cacheKey, 1800, function () use ($identifiers, $metadataPrefix) {
            $records = [];
            $promises = [];
            
            // Split into chunks to avoid too many concurrent requests
            $chunks = array_chunk($identifiers, $this->maxConcurrentRequests);
            
            foreach ($chunks as $chunk) {
                foreach ($chunk as $identifier) {
                    $promises[$identifier] = $this->client->getAsync($this->endpoint, [
                        'query' => [
                            'verb' => 'GetRecord',
                            'identifier' => $identifier,
                            'metadataPrefix' => $metadataPrefix,
                        ]
                    ]);
                }
                
                // Wait for current chunk to complete
                $responses = Promise\Utils::settle($promises)->wait();
                
                foreach ($responses as $identifier => $response) {
                    if ($response['state'] === 'fulfilled') {
                        try {
                            $xml = simplexml_load_string((string) $response['value']->getBody(), 
                                'SimpleXMLElement', LIBXML_NOCDATA);
                            $record = $this->parseSingleRecord($xml->GetRecord->record);
                            $records[] = $record;
                        } catch (\Exception $e) {
                            Log::warning("Failed to parse record {$identifier}: " . $e->getMessage());
                        }
                    } else {
                        Log::warning("Failed to fetch record {$identifier}: " . 
                            $response['reason']->getMessage());
                    }
                }
                
                $promises = []; // Reset for next chunk
            }
            
            return $records;
        });
    }

    /**
     * Stream records using generator for memory efficiency
     */
    public function streamRecords($params = [])
    {
        $defaultParams = [
            'verb' => 'ListRecords',
            'metadataPrefix' => 'oai_dc',
            'set' => null,
            'from' => null,
            'until' => null,
        ];

        $params = array_merge($defaultParams, $params);
        $params = array_filter($params, function ($value) {
            return $value !== null;
        });

        $hasMore = true;
        $requestParams = $params;

        while ($hasMore) {
            try {
                $response = $this->makeRequest($requestParams);
                $records = $this->parseListRecords($response);
                
                foreach ($records['records'] as $record) {
                    yield $record;
                }
                
                if (!empty($records['resumptionToken'])) {
                    $requestParams = ['resumptionToken' => $records['resumptionToken']];
                } else {
                    $hasMore = false;
                }
                
            } catch (\Exception $e) {
                Log::error('Streaming failed: ' . $e->getMessage());
                $hasMore = false;
            }
        }
    }

    /**
     * Search using streaming for better memory usage
     */
    private function searchStreaming($params = [])
    {
        $maxResults = $params['maxResults'] ?? 100;
        $keyword = $params['keyword'] ?? null;
        
        unset($params['keyword'], $params['maxResults']);
        
        $count = 0;
        $results = [];
        
        foreach ($this->streamRecords($params) as $record) {
            // Apply keyword filter if needed
            if ($keyword && !$this->recordMatchesKeyword($record, $keyword)) {
                continue;
            }
            
            $results[] = $record;
            $count++;
            
            if ($count >= $maxResults) {
                break;
            }
        }
        
        return [
            'records' => $results,
            'total' => count($results),
            'hasMore' => $count >= $maxResults,
        ];
    }

    /**
     * Check if a single record matches keyword
     */
    private function recordMatchesKeyword($record, $keyword)
    {
        $keyword = strtolower(trim($keyword));
        $keywords = preg_split('/\s+/', $keyword);
        $metadata = $record['metadata'] ?? [];
        $searchText = '';
        
        $searchableFields = ['title', 'description', 'subject', 'creator'];
        foreach ($searchableFields as $field) {
            if (isset($metadata[$field])) {
                $searchText .= ' ' . (is_array($metadata[$field]) 
                    ? implode(' ', $metadata[$field]) 
                    : (string) $metadata[$field]);
            }
        }
        
        $searchText = strtolower($searchText);
        
        foreach ($keywords as $kw) {
            if (strpos($searchText, $kw) === false) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get specific records by identifiers with caching
     */
    public function getRecordsByIdentifiers($identifiers, $metadataPrefix = 'oai_dc')
    {
        if (count($identifiers) > $this->maxConcurrentRequests) {
            return $this->getRecordsByIdentifiersParallel($identifiers, $metadataPrefix);
        }
        
        $cacheKey = 'oai_records_ids_' . md5($this->endpoint . json_encode($identifiers) . $metadataPrefix);
        
        return Cache::remember($cacheKey, 1800, function () use ($identifiers, $metadataPrefix) {
            $records = [];
            
            foreach ($identifiers as $identifier) {
                try {
                    $record = $this->getRecord($identifier, $metadataPrefix);
                    $records[] = $this->parseSingleRecord($record->GetRecord->record);
                } catch (\Exception $e) {
                    Log::warning("Failed to fetch record {$identifier}: " . $e->getMessage());
                    continue;
                }
            }
            
            return $records;
        });
    }

    /**
     * Parse record for GetRecord response
     */
    private function parseRecord($xml)
    {
        return $xml;
    }

    /**
     * Get repository statistics quickly
     */
    public function getStatistics()
    {
        $cacheKey = 'oai_stats_' . md5($this->endpoint);
        
        return Cache::remember($cacheKey, 7200, function () {
            $stats = [
                'totalRecords' => 0,
                'earliestDate' => null,
                'latestDate' => null,
                'sets' => 0,
                'formats' => 0,
            ];
            
            try {
                $identify = $this->identify();
                $sets = $this->listSets();
                $formats = $this->listMetadataFormats();
                
                $stats['sets'] = count($sets);
                $stats['formats'] = count($formats);
                $stats['earliestDate'] = $identify['earliestDatestamp'] ?? null;
                
                // Try to get rough count from first ListRecords request
                try {
                    $firstBatch = $this->listRecords(['metadataPrefix' => 'oai_dc']);
                    if (isset($firstBatch['completeListSize'])) {
                        $stats['totalRecords'] = $firstBatch['completeListSize'];
                    } else {
                        // Estimate from resumption token
                        $stats['totalRecords'] = 'unknown (use estimateFromSample method)';
                    }
                } catch (\Exception $e) {
                    Log::warning("Could not estimate total records: " . $e->getMessage());
                }
                
            } catch (\Exception $e) {
                Log::error("Failed to get statistics: " . $e->getMessage());
            }
            
            return $stats;
        });
    }

    /**
     * Estimate total records by sampling
     */
    public function estimateTotalRecords()
    {
        $cacheKey = 'oai_estimate_' . md5($this->endpoint);
        
        return Cache::remember($cacheKey, 86400, function () { // Cache for 24 hours
            try {
                // Get first and last resumption token cursor
                $firstBatch = $this->listRecords(['metadataPrefix' => 'oai_dc']);
                
                if (isset($firstBatch['completeListSize'])) {
                    return $firstBatch['completeListSize'];
                }
                
                if (isset($firstBatch['resumptionToken'])) {
                    // Make a few requests to estimate
                    $cursor = $firstBatch['cursor'] ?? 0;
                    $batchSize = count($firstBatch['records']);
                    
                    if ($batchSize > 0 && $cursor > 0) {
                        // Estimate based on cursor position
                        return (int) (($cursor / $batchSize) * $batchSize * 1.5);
                    }
                }
                
                // Fallback: count all records (slow)
                $count = 0;
                foreach ($this->streamRecords(['metadataPrefix' => 'oai_dc']) as $record) {
                    $count++;
                    if ($count > 10000) { // Safety limit
                        return "> $count";
                    }
                }
                
                return $count;
                
            } catch (\Exception $e) {
                Log::error("Estimation failed: " . $e->getMessage());
                return 'unknown';
            }
        });
    }
}