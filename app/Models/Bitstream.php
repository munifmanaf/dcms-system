<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitstream extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'internal_id', 'mime_type', 'size_bytes', 'checksum',
        'checksum_algorithm', 'sequence_id', 'item_id', 'bitstream_format_id',
        'bundle_name', 'description', 'file_version', 'is_current', 'replaces_bitstream_id',
        'download_count', 'original_filename', 'file_extension', 'technical_metadata'
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'technical_metadata' => 'array',
        'is_current' => 'boolean',
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function bitstreamFormat()
    {
        return $this->belongsTo(BitstreamFormat::class);
    }

    // Scopes
    public function scopeOriginal($query)
    {
        return $query->where('bundle_name', 'ORIGINAL');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_id');
    }

     public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->size_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file icon based on extension
     */
    public function getFileIconAttribute(): string
    {
        $extension = strtolower($this->file_extension);
        
        $icons = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'txt' => 'fa-file-alt',
            'csv' => 'fa-file-csv',
        ];

        return $icons[$extension] ?? 'fa-file';
    }

    /**
     * Increment download count
     */
    public function incrementDownloads()
    {
        $this->increment('download_count');
        $this->item->incrementDownloads();
    }

    /**
     * Scope for current bitstreams
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Create new version of this bitstream
     */
    public function createNewVersion($newFile)
    {
        // Mark current as not current
        $this->update(['is_current' => false]);

        // Create new bitstream
        $newBitstream = $this->replicate();
        $newBitstream->file_version = $this->file_version + 1;
        $newBitstream->is_current = true;
        $newBitstream->replaces_bitstream_id = $this->id;
        $newBitstream->download_count = 0;
        
        // Update with new file data
        // This would be handled in the controller when uploading new file
        
        return $newBitstream;
    }
}