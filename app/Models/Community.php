<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Community extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'is_public', 'sort_order'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($community) {
            $community->slug = Str::slug($community->name);
        });

        static::updating(function ($community) {
            if ($community->isDirty('name')) {
                $community->slug = Str::slug($community->name);
            }
        });
    }

    // Relationships
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // app/Models/Community.php
    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }
}