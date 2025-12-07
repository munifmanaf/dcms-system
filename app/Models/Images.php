// app/Models/Image.php (update existing)
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $fillable = [
        'user_id',
        'original_name',
        'stored_name',
        'paths',
        'mime_type',
        'size',
        'metadata',
        'dimensions',
        'has_watermark',
        'is_optimized',
        'imageable_id',
        'imageable_type',
        'is_featured',
        'alt_text',
        'caption',
        'order',
    ];
    
    protected $casts = [
        'paths' => 'array',
        'metadata' => 'array',
        'dimensions' => 'array',
        'has_watermark' => 'boolean',
        'is_optimized' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the parent imageable model
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get URL for specific image size
     */
    public function getUrl($size = 'original')
    {
        $path = $this->paths[$size] ?? $this->paths['original'];
        return Storage::disk('public')->url($path);
    }
    
    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->getUrl('thumbnail');
    }
}