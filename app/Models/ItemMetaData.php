<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'key',
        'value',
        'type'
    ];

    protected $casts = [
        'value' => 'string' // We'll handle casting manually
    ];

    /**
     * Get the item this metadata belongs to
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get casted value based on type
     */
    public function getCastedValueAttribute()
    {
        return match($this->type) {
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'boolean' => (bool) $this->value,
            'json' => json_decode($this->value, true),
            'date' => \Carbon\Carbon::parse($this->value),
            default => (string) $this->value,
        };
    }
}