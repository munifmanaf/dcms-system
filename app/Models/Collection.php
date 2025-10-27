<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Collection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'community_id', 'is_public', 'sort_order'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($collection) {
            $collection->slug = Str::slug($collection->name);
        });

        static::updating(function ($collection) {
            if ($collection->isDirty('name')) {
                $collection->slug = Str::slug($collection->name);
            }
        });
    }

    // Relationships
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function metadataFields()
    {
        return $this->hasMany(MetadataField::class)->active()->ordered();
    }

    /**
     * Get required metadata fields
     */
    public function getRequiredMetadataFields()
    {
        return $this->metadataFields()->where('is_required', true)->get();
    }

    /**
     * Check if collection has metadata fields
     */
    public function hasMetadataFields(): bool
    {
        return $this->metadataFields()->count() > 0;
    }
}