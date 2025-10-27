<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email', 
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the items submitted by this user
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'user_id');
    }

    /**
     * Get the workflow actions performed by this user
     */
    public function workflowActions()
    {
        return $this->hasMany(WorkflowAction::class);
    }

    /**
     * Check if user has specific role(s)
     */
    public function hasRole($roles)
    {
        if (!is_array($roles)) {
            $roles = explode(',', $roles);
        }
        
        return in_array($this->role, array_map('trim', $roles));
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        if (!is_array($roles)) {
            $roles = explode(',', $roles);
        }
        
        return in_array($this->role, array_map('trim', $roles));
    }

    /**
     * Check if user can perform specific workflow action
     */
    public function canPerformWorkflowAction($action)
    {
        $permissions = [
            'user' => ['submit'],
            'technical_reviewer' => ['technical_review'],
            'content_reviewer' => ['content_review'],
            'manager' => ['final_approve', 'quick_approve'],
            'admin' => ['final_approve', 'quick_approve', 'manage_users', 'system_settings'],
        ];

        return in_array($action, $permissions[$this->role] ?? []);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager or admin
     */
    public function isManagerOrAdmin()
    {
        return in_array($this->role, ['manager', 'admin']);
    }

    /**
     * Check if user is reviewer (technical or content)
     */
    public function isReviewer()
    {
        return in_array($this->role, ['technical_reviewer', 'content_reviewer']);
    }
}