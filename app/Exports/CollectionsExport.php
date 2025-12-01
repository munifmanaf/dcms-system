<?php

namespace App\Exports;

use App\Models\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CollectionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Collection::select('id', 'name', 'description', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name', 
            'Description',
            'Created At'
        ];
    }
}