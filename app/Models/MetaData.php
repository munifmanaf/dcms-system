<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetadataTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'data_type', 
        'fields',
        'is_active'
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get items using this template
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'data_type', 'data_type');
    }

    /**
     * Get field names as array
     */
    public function getFieldNamesAttribute()
    {
        return collect($this->fields)->pluck('name')->toArray();
    }

    /**
     * Get required fields
     */
    public function getRequiredFieldsAttribute()
    {
        return collect($this->fields)
            ->where('required', true)
            ->pluck('name')
            ->toArray();
    }
}