<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'version_number',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'title',
        'description',
        'metadata',
        'changes',
        'user_id',
        'is_autosave',
        'restored_from_id'
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
        'is_autosave' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship with Item
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship with User who created the version
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with the version this was restored from
     */
    public function restoredFrom(): BelongsTo
    {
        return $this->belongsTo(ItemVersion::class, 'restored_from_id');
    }

    /**
     * Scope for manual versions (excluding autosaves)
     */
    public function scopeManual($query)
    {
        return $query->where('is_autosave', false);
    }

    /**
     * Scope for autosave versions
     */
    public function scopeAutosave($query)
    {
        return $query->where('is_autosave', true);
    }

    /**
     * Scope ordered by version number (descending)
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByRaw('CAST(version_number AS UNSIGNED) DESC');
    }

    /**
     * Get the file URL for this version
     */
    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    /**
     * Get the file extension
     */
    public function getFileExtensionAttribute()
    {
        return $this->file_path ? pathinfo($this->file_path, PATHINFO_EXTENSION) : null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 2) . ' MB';
        } elseif ($this->file_size >= 1024) {
            return number_format($this->file_size / 1024, 2) . ' KB';
        } else {
            return $this->file_size . ' bytes';
        }
    }

    /**
     * Check if this version has a file
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path) && Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Compare this version with another version or current item
     */
    public function compareWith($other): array
    {
        $differences = [];

        // Compare basic fields
        $fields = ['title', 'description'];
        foreach ($fields as $field) {
            if ($this->$field != $other->$field) {
                $differences[$field] = [
                    'from' => $this->$field,
                    'to' => $other->$field
                ];
            }
        }

        // Compare metadata
        if ($this->metadata != $other->metadata) {
            $differences['metadata'] = [
                'from' => $this->metadata,
                'to' => $other->metadata
            ];
        }

        // Compare file
        if ($this->file_name != $other->file_name || $this->file_size != $other->file_size) {
            $differences['file'] = [
                'from' => [
                    'name' => $this->file_name,
                    'size' => $this->file_size
                ],
                'to' => [
                    'name' => $other->file_name,
                    'size' => $other->file_size
                ]
            ];
        }

        return $differences;
    }

    /**
     * Generate next version number for an item
     */
    public static function generateNextVersionNumber(Item $item): string
    {
        $latestVersion = $item->versions()->manual()->latestFirst()->first();
        
        if (!$latestVersion) {
            return '1.0';
        }

        $currentVersion = (float) $latestVersion->version_number;
        return number_format($currentVersion + 0.1, 1);
    }
}