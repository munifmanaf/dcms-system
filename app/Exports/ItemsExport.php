<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsExport implements FromCollection, WithHeadings
{
    public $collectionId = null;

    public function collection()
    {
        $query = Item::with('collection');
        
        if ($this->collectionId) {
            $query->where('collection_id', $this->collectionId);
        }

        return $query->select('id', 'title', 'description', 'collection_id', 'file_type', 'download_count', 'view_count', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Description', 
            'Collection ID',
            'File Type',
            'Download Count',
            'View Count',
            'Created At'
        ];
    }
}

