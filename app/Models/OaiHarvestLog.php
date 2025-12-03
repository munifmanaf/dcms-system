<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OaiHarvestLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint',
        'metadata_prefix',
        'set_spec',
        'from_date',
        'until_date',
        'status',
        'total_records',
        'imported_records',
        'skipped_records',
        'failed_records',
        'resumption_token',
        'error_message',
        'parameters',
        'user_id',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'from_date' => 'datetime',
        'until_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'harvest_log_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Methods
    public function getSuccessRateAttribute()
    {
        if ($this->total_records === 0) {
            return 0;
        }
        
        return ($this->imported_records / $this->total_records) * 100;
    }

    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }
        
        return $this->completed_at->diff($this->started_at);
    }
}