<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OAIController extends Controller
{
    public function index(Request $request)
    {
        try {
            $verb = $request->get('verb', 'Identify');
            
            switch ($verb) {
                case 'Identify':
                    return $this->identify();
                case 'ListMetadataFormats':
                    return $this->listMetadataFormats();
                case 'ListSets':
                    return $this->listSets();
                case 'ListRecords':
                    return $this->listRecords($request);
                case 'ListIdentifiers':
                    return $this->listIdentifiers($request);
                case 'GetRecord':
                    return $this->getRecord($request);
                default:
                    return $this->error('badVerb', 'Illegal OAI-PMH verb');
            }
        } catch (\Exception $e) {
            Log::error('OAI-PMH Error: ' . $e->getMessage());
            return $this->error('badArgument', 'Internal server error');
        }
    }

    private function identify()
    {
        try {
            $earliestItem = \App\Models\Item::orderBy('created_at')->first();
            $earliestDatestamp = $earliestItem ? $earliestItem->created_at : now();
        } catch (\Exception $e) {
            $earliestDatestamp = now();
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>'.now()->format('Y-m-d\TH:i:s\Z').'</responseDate>
    <request verb="Identify">'.url('/oai').'</request>
    <Identify>
        <repositoryName>'.config('app.name', 'DCMS Repository').'</repositoryName>
        <baseURL>'.url('/oai').'</baseURL>
        <protocolVersion>2.0</protocolVersion>
        <adminEmail>'.config('mail.from.address', 'admin@dcms.test').'</adminEmail>
        <earliestDatestamp>'.$this->formatDate($earliestDatestamp).'</earliestDatestamp>
        <deletedRecord>no</deletedRecord>
        <granularity>YYYY-MM-DDThh:mm:ssZ</granularity>
    </Identify>
</OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function listMetadataFormats()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
    <responseDate>'.now()->format('Y-m-d\TH:i:s\Z').'</responseDate>
    <request verb="ListMetadataFormats">'.url('/oai').'</request>
    <ListMetadataFormats>
        <metadataFormat>
            <metadataPrefix>oai_dc</metadataPrefix>
            <schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>
            <metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/</metadataNamespace>
        </metadataFormat>
    </ListMetadataFormats>
</OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function listSets()
    {
        try {
            $collections = \App\Models\Collection::all();
            
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
    <responseDate>'.now()->format('Y-m-d\TH:i:s\Z').'</responseDate>
    <request verb="ListSets">'.url('/oai').'</request>
    <ListSets>';
            
            foreach ($collections as $collection) {
                $xml .= '
        <set>
            <setSpec>collection_'.$collection->id.'</setSpec>
            <setName>'.htmlspecialchars($collection->name).'</setName>
        </set>';
            }
            
            $xml .= '
    </ListSets>
</OAI-PMH>';

        } catch (\Exception $e) {
            // Fallback if collections fail
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
    <responseDate>'.now()->format('Y-m-d\TH:i:s\Z').'</responseDate>
    <request verb="ListSets">'.url('/oai').'</request>
    <ListSets>
        <set>
            <setSpec>default</setSpec>
            <setName>Default Collection</setName>
        </set>
    </ListSets>
</OAI-PMH>';
        }

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function listRecords(Request $request)
    {
        try {
            // Use is_published instead of is_public
            $items = \App\Models\Item::where('workflow_state', 'published')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
    <responseDate>'.now()->format('Y-m-d\TH:i:s\Z').'</responseDate>
    <request verb="ListRecords">'.url('/oai').'</request>
    <ListRecords>';
            
            foreach ($items as $item) {
                // Parse metadata JSON if it exists
                $metadata = json_decode($item->metadata, true) ?? [];
                
                $xml .= '
        <record>
            <header>
                <identifier>oai:dcms:item-'.$item->id.'</identifier>
                <datestamp>'.$this->formatDate($item->updated_at).'</datestamp>
                <setSpec>collection_'.$item->collection_id.'</setSpec>
            </header>
            <metadata>
                <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/">
                    <dc:title>'.htmlspecialchars($item->title).'</dc:title>
                    <dc:description>'.htmlspecialchars($item->description).'</dc:description>
                    <dc:type>'.htmlspecialchars($item->file_type ?? 'Digital Item').'</dc:type>
                    <dc:format>'.htmlspecialchars($item->file_type ?? 'digital').'</dc:format>
                    <dc:date>'.$this->formatDate($item->created_at).'</dc:date>
                    <dc:identifier>'.url('/items/'.$item->id).'</dc:identifier>';
                
                // Add metadata from your metadata JSON field
                if (isset($metadata['dc_creator']) && !empty($metadata['dc_creator'])) {
                    $creators = is_array($metadata['dc_creator']) ? $metadata['dc_creator'] : [$metadata['dc_creator']];
                    foreach ($creators as $creator) {
                        $xml .= '
                    <dc:creator>'.htmlspecialchars($creator).'</dc:creator>';
                    }
                }
                
                if (isset($metadata['dc_subject']) && !empty($metadata['dc_subject'])) {
                    $subjects = is_array($metadata['dc_subject']) ? $metadata['dc_subject'] : [$metadata['dc_subject']];
                    foreach ($subjects as $subject) {
                        $xml .= '
                    <dc:subject>'.htmlspecialchars($subject).'</dc:subject>';
                    }
                }
                
                $xml .= '
                </oai_dc:dc>
            </metadata>
        </record>';
            }
            
            $xml .= '
    </ListRecords>
</OAI-PMH>';

        } catch (\Exception $e) {
            return $this->error('noRecordsMatch', 'No records available');
        }

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function listIdentifiers(Request $request)
    {
        try {
            $items = \App\Models\Item::where('workflow_state', 'published')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
    <responseDate>'.now()->format('Y-m-d\TH:i:s\Z').'</responseDate>
    <request verb="ListIdentifiers">'.url('/oai').'</request>
    <ListIdentifiers>';
            
            foreach ($items as $item) {
                $xml .= '
        <header>
            <identifier>oai:dcms:item-'.$item->id.'</identifier>
            <datestamp>'.$this->formatDate($item->updated_at).'</datestamp>
            <setSpec>collection_'.$item->collection_id.'</setSpec>
        </header>';
            }
            
            $xml .= '
    </ListIdentifiers>
</OAI-PMH>';

        } catch (\Exception $e) {
            return $this->error('noRecordsMatch', 'No records available');
        }

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function getRecord(Request $request)
    {
        $identifier = $request->get('identifier');
        
        if (!$identifier) {
            return $this->error('badArgument', 'Missing identifier parameter');
        }

        try {
            // Extract item ID from identifier (oai:dcms:item-123)
            $itemId = str_replace('oai:dcms:item-', '', $identifier);
            
            $item = \App\Models\Item::where('id', $itemId)->where('workflow_state', 'published')->first();

            if (!$item) {
                return $this->error('idDoesNotExist', 'The specified record does not exist');
            }

            // Parse metadata JSON
            $metadata = json_decode($item->metadata, true) ?? [];

            $xml = '<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
    <responseDate>'.now()->format('Y-m-d\TH:i:s\Z').'</responseDate>
    <request verb="GetRecord">'.url('/oai').'</request>
    <GetRecord>
        <record>
            <header>
                <identifier>oai:dcms:item-'.$item->id.'</identifier>
                <datestamp>'.$this->formatDate($item->updated_at).'</datestamp>
                <setSpec>collection_'.$item->collection_id.'</setSpec>
            </header>
            <metadata>
                <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/">
                    <dc:title>'.htmlspecialchars($item->title).'</dc:title>
                    <dc:description>'.htmlspecialchars($item->description).'</dc:description>
                    <dc:type>'.htmlspecialchars($item->file_type ?? 'Digital Item').'</dc:type>
                    <dc:format>'.htmlspecialchars($item->file_type ?? 'digital').'</dc:format>
                    <dc:date>'.$this->formatDate($item->created_at).'</dc:date>
                    <dc:identifier>'.url('/items/'.$item->id).'</dc:identifier>';
            
            // Add metadata from JSON field
            if (isset($metadata['dc_creator']) && !empty($metadata['dc_creator'])) {
                $creators = is_array($metadata['dc_creator']) ? $metadata['dc_creator'] : [$metadata['dc_creator']];
                foreach ($creators as $creator) {
                    $xml .= '
                    <dc:creator>'.htmlspecialchars($creator).'</dc:creator>';
                }
            }
            
            if (isset($metadata['dc_subject']) && !empty($metadata['dc_subject'])) {
                $subjects = is_array($metadata['dc_subject']) ? $metadata['dc_subject'] : [$metadata['dc_subject']];
                foreach ($subjects as $subject) {
                    $xml .= '
                    <dc:subject>'.htmlspecialchars($subject).'</dc:subject>';
                }
            }
            
            $xml .= '
                </oai_dc:dc>
            </metadata>
        </record>
    </GetRecord>
</OAI-PMH>';

        } catch (\Exception $e) {
            return $this->error('idDoesNotExist', 'Error retrieving record');
        }

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function error($code, $message)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/">
    <responseDate>'.now()->format('Y-m-d\TH:i:s\Z').'</responseDate>
    <request>'.url('/oai').'</request>
    <error code="'.$code.'">'.$message.'</error>
</OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    private function formatDate($date)
    {
        return $date->format('Y-m-d\TH:i:s\Z');
    }
}