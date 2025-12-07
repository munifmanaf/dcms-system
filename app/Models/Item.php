<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'collection_id',
        'user_id', // Make sure this is in fillable
        'is_published',
        'is_approved',
        'is_archived',
        'is_featured',
        'approved_by',
        'approved_at',
        'metadata',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'slug',
        'workflow_state',
        'oai_identifier', 'oai_datestamp', 'harvest_log_id', 'import_date',
        'source', 'accession_number', 'external_identifier', 'author',
        'publisher', 'item_type'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
        'is_archived' => 'boolean',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
        'external_identifier' => 'array'
    ];

    // Add this boot method if not already there
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title);
            }
        });
    }

    // ============================================================================
    // RELATIONSHIPS
    // ============================================================================

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Add this user relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship for the user who approved the item
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ============================================================================
    // WORKFLOW METHODS
    // ============================================================================

    public function getWorkflowStatusColor()
    {
        if ($this->is_published) return 'success';
        if ($this->is_approved) return 'info';
        if ($this->is_archived) return 'secondary';
        return 'warning';
    }

    public function getWorkflowStatusText()
    {
        if ($this->is_published) return 'Published';
        if ($this->is_approved) return 'Approved';
        if ($this->is_archived) return 'Archived';
        return 'Draft';
    }

    public function getCurrentWorkflowStep()
    {
        if ($this->is_published) return 'published';
        if ($this->is_approved) return 'approved';
        if ($this->is_archived) return 'archived';
        return 'draft';
    }

    public function isWorkflowStepCompleted($step)
    {
        $completedSteps = ['draft']; // Always completed
        
        if ($this->is_published) {
            $completedSteps = array_merge($completedSteps, ['submitted', 'technical_review', 'content_review', 'approved', 'published']);
        } elseif ($this->is_approved) {
            $completedSteps = array_merge($completedSteps, ['submitted', 'technical_review', 'content_review', 'approved']);
        } elseif ($this->is_archived) {
            $completedSteps = array_merge($completedSteps, ['archived']);
        }
        
        return in_array($step, $completedSteps);
    }

    public function canBeSubmitted()
    {
        return !$this->is_published && !$this->is_approved && !$this->is_archived;
    }

    public function isInReview()
    {
        // You can customize this based on your actual workflow status field
        return $this->workflow_status === 'in_review' || 
               $this->workflow_status === 'technical_review' || 
               $this->workflow_status === 'content_review';
    }

    public function isReadyForApproval()
    {
        // Customize based on your workflow logic
        return $this->workflow_status === 'ready_for_approval' || 
               ($this->isInReview() && !$this->is_approved);
    }

    public function getFileTypeDisplay()
    {
        if (!$this->file_type) return 'Text Content';
        
        $type = strtolower($this->file_type);
        
        if (str_contains($type, 'image')) return 'Image';
        if (str_contains($type, 'pdf')) return 'PDF';
        if (str_contains($type, 'word') || str_contains($type, 'document')) return 'Document';
        if (str_contains($type, 'excel') || str_contains($type, 'spreadsheet')) return 'Spreadsheet';
        if (str_contains($type, 'video')) return 'Video';
        if (str_contains($type, 'audio')) return 'Audio';
        if (str_contains($type, 'text')) return 'Text';
        
        // Extract extension from file name as fallback
        if ($this->file_name) {
            $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
            return strtoupper($extension) . ' File';
        }
        
        return 'File';
    }

    // Add these methods to your Item model

/**
 * Get completed steps count for progress
 */
    public function getCompletedStepsCount()
    {
        $steps = ['draft', 'submitted', 'technical_review', 'content_review', 'approved', 'published'];
        $completed = 0;
        
        foreach ($steps as $step) {
            if ($this->isWorkflowStepCompleted($step)) {
                $completed++;
            }
        }
        
        return $completed;
    }

    /**
     * Get workflow progress percentage
     */
    public function getWorkflowProgressPercentage()
    {
        $totalSteps = 6; // draft, submitted, technical_review, content_review, approved, published
        $completed = $this->getCompletedStepsCount();
        
        return round(($completed / $totalSteps) * 100);
    }

   // Add to your Item model

    /**
     * Get workflow status icon
     */
    public function getWorkflowStatusIcon()
    {
        if ($this->is_published) return 'globe-americas';
        if ($this->is_approved) return 'check-circle';
        if ($this->is_archived) return 'archive';
        return 'edit';
    }

    /**
     * Get next step name
     */
    public function getNextStepName()
    {
        $steps = ['draft', 'submitted', 'technical_review', 'content_review', 'approved', 'published'];
        $currentStep = $this->getCurrentWorkflowStep();
        $currentIndex = array_search($currentStep, $steps);
        
        if ($currentIndex !== false && isset($steps[$currentIndex + 1])) {
            $nextStep = $steps[$currentIndex + 1];
            
            $stepNames = [
                'draft' => 'Submit for Review',
                'submitted' => 'Technical Review',
                'technical_review' => 'Content Review', 
                'content_review' => 'Final Approval',
                'approved' => 'Publish',
                'published' => 'Completed'
            ];
            
            return $stepNames[$nextStep] ?? 'Next Step';
        }
        
        return 'Completed';
    }

    /**
     * Get next action description
     */
    public function getNextActionDescription()
    {
        $currentStep = $this->getCurrentWorkflowStep();
        
        $descriptions = [
            'draft' => 'Submit this item to begin the review process',
            'submitted' => 'Technical team needs to review the specifications',
            'technical_review' => 'Content team should review for quality and accuracy', 
            'content_review' => 'Final approval required before publication',
            'approved' => 'Ready to publish and make this item live',
            'published' => 'Workflow completed successfully'
        ];
        
        return $descriptions[$currentStep] ?? 'Continue to next step';
    }

    /**
     * Get estimated completion time
     */
    public function getEstimatedCompletion()
    {
        $stepsLeft = 6 - $this->getCompletedStepsCount();
        
        if ($stepsLeft === 0) return 'Complete';
        if ($stepsLeft === 1) return '1-2 days';
        if ($stepsLeft === 2) return '3-5 days';
        if ($stepsLeft === 3) return '1 week';
        
        return '1-2 weeks';
    }

    public function versions()
    {
        return $this->hasMany(ItemVersion::class)->latestFirst();
    }

    /**
     * Get only manual versions (excluding autosaves)
     */
    public function manualVersions()
    {
        return $this->hasMany(ItemVersion::class)->manual()->latestFirst();
    }

    /**
     * Get autosave versions
     */
    public function autosaveVersions()
    {
        return $this->hasMany(ItemVersion::class)->autosave()->latestFirst();
    }

    /**
     * Get the latest version
     */
    public function getLatestVersionAttribute()
    {
        return $this->versions()->first();
    }

    /**
     * Get the latest manual version
     */
    public function getLatestManualVersionAttribute()
    {
        return $this->manualVersions()->first();
    }

    /**
     * Check if item has versions
     */
    public function hasVersions(): bool
    {
        return $this->versions()->exists();
    }

    /**
     * Get version count
     */
    public function getVersionCountAttribute(): int
    {
        return $this->versions()->count();
    }

    /**
     * Create a new version snapshot
     */
    public function createVersion(string $changes = null, bool $isAutosave = false, User $user = null): ItemVersion
    {
        $user = $user ?? auth()->user();
        $versionNumber = $isAutosave ? 'autosave' : ItemVersion::generateNextVersionNumber($this);

        return ItemVersion::create([
            'item_id' => $this->id,
            'version_number' => $versionNumber,
            'file_path' => $this->file_path,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'title' => $this->title,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'changes' => $changes,
            'user_id' => $user->id,
            'is_autosave' => $isAutosave,
        ]);
    }

    /**
     * Restore from a specific version
     */
    public function restoreFromVersion(ItemVersion $version, User $user = null): bool
    {
        $user = $user ?? auth()->user();

        // Create a version of current state before restoring
        $this->createVersion("Before restoring to version {$version->version_number}", false, $user);

        // Restore the item data
        $this->update([
            'title' => $version->title,
            'description' => $version->description,
            'metadata' => $version->metadata,
            'file_path' => $version->file_path,
            'file_name' => $version->file_name,
            'file_size' => $version->file_size,
            'file_type' => $version->file_type,
        ]);

        // Create a restoration version
        ItemVersion::create([
            'item_id' => $this->id,
            'version_number' => ItemVersion::generateNextVersionNumber($this),
            'file_path' => $this->file_path,
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'title' => $this->title,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'changes' => "Restored from version {$version->version_number}",
            'user_id' => $user->id,
            'is_autosave' => false,
            'restored_from_id' => $version->id,
        ]);

        return true;
    }

    // Correct way to define the attribute
    public function getHasVersionsAttribute()
    {
        return method_exists($this, 'versions') && $this->versions()->exists();
    }

    public function harvestLog()
    {
        return $this->belongsTo(OaiHarvestLog::class, 'harvest_log_id');
    }

    public function scopeOaiItems($query)
    {
        return $query->where('source', 'oai-pmh');
    }

    // Scope for manual items
    public function scopeManualItems($query)
    {
        return $query->where('source', 'manual');
    }

    // Get specific metadata value
    public function getMetadataValue($key, $default = null)
    {
        $metadata = $this->metadata;
        
        // Check direct key
        if (isset($metadata[$key])) {
            $value = $metadata[$key];
            return is_array($value) && !empty($value) ? $value[0] : $value;
        }
        
        // Check Dublin Core format
        $dcKey = 'dc_' . $key;
        if (isset($metadata[$dcKey])) {
            $value = $metadata[$dcKey];
            return is_array($value) && !empty($value) ? $value[0] : $value;
        }
        
        return $default;
    }

    // Get all metadata values for a key
    public function getMetadataArray($key)
    {
        $metadata = $this->metadata;
        $value = $this->getMetadataValue($key, []);
        
        if (is_array($value)) {
            return $value;
        }
        
        return $value ? [$value] : [];
    }

    // In app/Models/Item.php
    // Add these methods to handle your metadata format

    /**
     * Get DC metadata value
     */
    public function getDcValue($field, $default = null)
    {
        $metadata = $this->metadata;
        $dcField = 'dc_' . $field;
        
        if (isset($metadata[$dcField]) && !empty($metadata[$dcField])) {
            if (is_array($metadata[$dcField])) {
                return count($metadata[$dcField]) === 1 ? $metadata[$dcField][0] : $metadata[$dcField];
            }
            return $metadata[$dcField];
        }
        
        return $default;
    }

    /**
     * Get DC metadata as array
     */
    public function getDcArray($field)
    {
        $value = $this->getDcValue($field, []);
        
        if (is_array($value)) {
            return $value;
        }
        
        return $value ? [$value] : [];
    }

    /**
     * Set DC metadata value
     */
    public function setDcValue($field, $value)
    {
        $metadata = $this->metadata;
        $dcField = 'dc_' . $field;
        
        if (is_array($value)) {
            $metadata[$dcField] = $value;
        } else {
            $metadata[$dcField] = [$value];
        }
        
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Add value to DC array metadata
     */
    public function addDcValue($field, $value)
    {
        $metadata = $this->metadata;
        $dcField = 'dc_' . $field;
        
        if (!isset($metadata[$dcField])) {
            $metadata[$dcField] = [];
        }
        
        if (!is_array($metadata[$dcField])) {
            $metadata[$dcField] = [$metadata[$dcField]];
        }
        
        if (is_array($value)) {
            $metadata[$dcField] = array_merge($metadata[$dcField], $value);
        } else {
            $metadata[$dcField][] = $value;
        }
        
        // Remove duplicates
        $metadata[$dcField] = array_unique($metadata[$dcField]);
        
        $this->metadata = $metadata;
        return $this;
    }

    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'thumbnail_id');
    }
    
    /**
     * Get all images attached to this item
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    
    /**
     * Get galleries this item belongs to
     */
    public function galleries(): BelongsToMany
    {
        return $this->belongsToMany(Gallery::class, 'gallery_items')
                    ->withTimestamps();
    }
    
    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return $this->thumbnail->getUrl('thumbnail');
        }
        
        // Return default image if no thumbnail
        return asset('images/default-thumbnail.jpg');
    }
    
    /**
     * Get medium image URL
     */
    public function getMediumImageUrlAttribute()
    {
        if ($this->thumbnail) {
            return $this->thumbnail->getUrl('medium');
        }
        
        return asset('images/default-medium.jpg');
    }
    
    /**
     * Get all image URLs for this item
     */
    public function getAllImageUrlsAttribute()
    {
        return $this->images->map(function($image) {
            return [
                'original' => $image->getUrl('original'),
                'thumbnail' => $image->getUrl('thumbnail'),
                'medium' => $image->getUrl('medium'),
                'large' => $image->getUrl('large'),
            ];
        });
    }

}