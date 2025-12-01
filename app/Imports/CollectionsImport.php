<?php

namespace App\Imports;

use App\Models\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CollectionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Collection([
            'name' => $row['title'] ?? '',
            'description' => $row['description'] ?? '',
        ]);
    }
}