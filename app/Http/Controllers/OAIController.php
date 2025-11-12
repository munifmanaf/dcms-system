<?php
// app/Http/Controllers/OAIController.php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OAIController extends Controller
{
    public function handleRequest(Request $request)
    {
        $verb = $request->get('verb', 'Identify');
        
        switch ($verb) {
            case 'Identify':
                return $this->identify();
            case 'ListRecords':
                return $this->listRecords($request);
            case 'ListIdentifiers':
                return $this->listIdentifiers($request);
            case 'GetRecord':
                return $this->getRecord($request);
            case 'ListSets':
                return $this->listSets();
            default:
                return $this->error('badVerb', 'Illegal OAI verb');
        }
    }

    private function identify()
    {
        return response()->xml([
            'Identify' => [
                'repositoryName' => config('app.name', 'Institutional Repository'),
                'baseURL' => route('oai.pmh'),
                'protocolVersion' => '2.0',
                'adminEmail' => config('mail.from.address', 'admin@repository.edu'),
                'earliestDatestamp' => Item::where('workflow_state', 'published')->min('created_at')?->format('Y-m-d\TH:i:s\Z') ?? now()->format('Y-m-d\TH:i:s\Z'),
                'deletedRecord' => 'no',
                'granularity' => 'YYYY-MM-DDThh:mm:ssZ'
            ]
        ]);
    }

    private function listRecords(Request $request)
    {
        $from = $request->get('from');
        $until = $request->get('until');
        $set = $request->get('set');
        $metadataPrefix = $request->get('metadataPrefix', 'oai_dc');

        if ($metadataPrefix !== 'oai_dc') {
            return $this->error('cannotDisseminateFormat', 'The metadata format is not supported');
        }

        $query = Item::where('workflow_state', 'published')
                    ->with(['collection.community']);

        if ($from) {
            $query->where('created_at', '>=', Carbon::parse($from));
        }

        if ($until) {
            $query->where('created_at', '<=', Carbon::parse($until));
        }

        if ($set) {
            $query->whereHas('collection', function($q) use ($set) {
                $q->where('id', $set);
            });
        }

        $items = $query->orderBy('created_at')->get();

        $records = $items->map(function($item) {
            return [
                'record' => [
                    'header' => [
                        'identifier' => 'oai:' . config('app.name') . ':' . $item->id,
                        'datestamp' => $item->created_at->format('Y-m-d\TH:i:s\Z'),
                        'setSpec' => $item->collection_id
                    ],
                    'metadata' => [
                        'oai_dc:dc' => array_merge([
                            'xmlns:oai_dc' => "http://www.openarchives.org/OAI/2.0/oai_dc/",
                            'xmlns:dc' => "http://purl.org/dc/elements/1.1/",
                            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
                            'xsi:schemaLocation' => "http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd"
                        ], $this->formatDublinCore($item))
                    ]
                ]
            ];
        });

        return response()->xml([
            'ListRecords' => $records->toArray()
        ]);
    }

    private function formatDublinCore($item)
    {
        $metadata = $item->metadata ?? [];
        
        return [
            'dc:title' => [$item->title],
            'dc:creator' => $metadata['dc_creator'] ?? [],
            'dc:subject' => $metadata['dc_subject'] ?? [],
            'dc:description' => $metadata['dc_description'] ?? [$item->description],
            'dc:publisher' => $metadata['dc_publisher'] ?? [config('app.name')],
            'dc:date' => $metadata['dc_date_issued'] ?? [$item->created_at->format('Y-m-d')],
            'dc:type' => $metadata['dc_type'] ?? ['Text'],
            'dc:format' => $metadata['dc_format'] ?? [$item->file_type],
            'dc:identifier' => $metadata['dc_identifier'] ?? [route('repository.item', $item->id)]
        ];
    }

    private function error($code, $message)
    {
        return response()->xml([
            'error' => [
                '_attributes' => ['code' => $code],
                '_value' => $message
            ]
        ]);
    }
}