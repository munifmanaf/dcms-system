<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitstreamFormat extends Model
{
    use HasFactory;

    protected $fillable = [
        'mimetype', 'short_description', 'description', 
        'support_level', 'internal', 'extensions'
    ];

    protected $casts = [
        'internal' => 'boolean',
    ];

    // Relationships
    public function bitstreams()
    {
        return $this->hasMany(Bitstream::class);
    }

    // Scopes
    public function scopeSupported($query)
    {
        return $query->where('support_level', 'SUPPORTED');
    }
}