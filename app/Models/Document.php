<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'content', 'file_path', 'file_name', 
        'file_size', 'file_type', 'is_published', 'published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Relationship with categories
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'document_category');
    }

    /**
     * Scope published documents
     */
    public function scopePublished($query)
    {
        return $query->where('workflow_state', 'published');
    }

    /**
     * Scope drafts
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Get documents by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    /**
     * Automatically generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            $document->slug = \Illuminate\Support\Str::slug($document->title);
        });

        static::updating(function ($document) {
            if ($document->isDirty('title')) {
                $document->slug = \Illuminate\Support\Str::slug($document->title);
            }
        });
    }
}