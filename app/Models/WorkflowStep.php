<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'action', 'order', 'allowed_roles', 'is_active', 'description'
    ];

    protected $casts = [
        'allowed_roles' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship with workflow actions
     */
    public function workflowActions()
    {
        return $this->hasMany(WorkflowAction::class);
    }

    /**
     * Scope active steps
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered steps
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Check if a role can perform this step
     */
    public function canBePerformedBy($role): bool
    {
        if (empty($this->allowed_roles)) {
            return true;
        }

        return in_array($role, $this->allowed_roles);
    }

    /**
     * Get next step in workflow
     */
    public function nextStep()
    {
        return self::active()->where('order', '>', $this->order)->ordered()->first();
    }

    /**
     * Get previous step in workflow
     */
    public function previousStep()
    {
        return self::active()->where('order', '<', $this->order)->ordered()->first();
    }
}