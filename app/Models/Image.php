<?php
// app/Models/Image.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = [
        'original_name',
        'stored_name',
        'path',
        'previews',
        'metadata',
        'extension',
        'size',
        'width',
        'height',
        'user_id',
        'item_id',
        'category',
        'description',
        'tags',
        'is_active',
        'is_public',
    ];
    
    protected $casts = [
        'previews' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];
    
    /**
     * Get the user that owns the image
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the item that owns the image
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
    
    /**
     * Get URL for original image
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }
    
    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        $previews = $this->previews ?? [];
        if (isset($previews['thm'])) {
            return Storage::url($previews['thm']['path']);
        }
        return $this->url; // Fallback to original
    }
    
    /**
     * Get preview URL
     */
    public function getPreviewUrlAttribute()
    {
        $previews = $this->previews ?? [];
        if (isset($previews['pre'])) {
            return Storage::url($previews['pre']['path']);
        }
        return $this->url; // Fallback to original
    }
    
    /**
     * Get screen URL
     */
    public function getScreenUrlAttribute()
    {
        $previews = $this->previews ?? [];
        if (isset($previews['scr'])) {
            return Storage::url($previews['scr']['path']);
        }
        return $this->url; // Fallback to original
    }
    
    /**
     * Check if image is an image file
     */
    public function getIsImageAttribute()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        return in_array(strtolower($this->extension), $imageExtensions);
    }
    
    /**
     * Get file size in human readable format
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Get dimensions as string
     */
    public function getDimensionsAttribute()
    {
        if ($this->width && $this->height) {
            return $this->width . ' × ' . $this->height;
        }
        return null;
    }
}