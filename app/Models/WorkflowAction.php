<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'user_id', 'workflow_step_id', 'action', 
        'comments', 'status', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Relationship with item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship with user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with workflow step
     */
    public function workflowStep()
    {
        return $this->belongsTo(WorkflowStep::class);
    }

    /**
     * Scope for specific action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get action description
     */
    public function getDescriptionAttribute(): string
    {
        $descriptions = [
            'submit' => 'Submitted for review',
            'technical_review' => 'Technical review completed',
            'content_review' => 'Content review completed',
            'approve' => 'Approved for publication',
            'publish' => 'Published',
            'reject' => 'Rejected',
            'return' => 'Returned for revisions',
        ];

        return $descriptions[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }
}