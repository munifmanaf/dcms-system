<?php
// app/Models/Repository.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'handle_prefix', 
        'contact_email',
        'logo',
        'copyright_text',
        // 'is_active'
    ];

    // protected $casts = [
    //     'is_active' => 'boolean'
    // ];

    /**
     * Get all communities belonging to this repository
     */
    public function communities()
    {
        return $this->hasMany(Community::class);
    }

    /**
     * Get all collections through communities
     */
    public function collections()
    {
        return Collection::whereIn('community_id', $this->communities()->pluck('id'));
    }

    /**
     * Get all items through collections and communities
     */
    public function items()
    {
        return Item::whereIn('collection_id', function($query) {
            $query->select('id')
                  ->from('collections')
                  ->whereIn('community_id', $this->communities()->pluck('id'));
        });
    }

    /**
     * Get published items count
     */
    public function getPublishedItemsCountAttribute()
    {
        return $this->items()->where('status', 'published')->count();
    }

    /**
     * Get recent items
     */
    public function recentItems($limit = 10)
    {
        return $this->items()
                    ->where('status', 'published')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Generate handle for items
     */
    public function generateHandle($itemId)
    {
        return $this->handle_prefix . '/' . $itemId;
    }

    /**
     * Get active repository (singleton for now)
     */
    // public static function getActive()
    // {
    //     return static::where('is_active', true)->first() ?? static::first();
    // }
}