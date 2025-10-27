<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MetadataField extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'name',
        'slug',
        'type',
        'is_required',
        'options',
        'default_value',
        'validation_rules',
        'help_text',
        'order',
        'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Relationship with Collection
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Scope active fields
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered fields
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Get validation rules for this field
     */
    public function getValidationRules(): array
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Add type-specific rules
        switch ($this->type) {
            case 'text':
                $rules[] = 'string';
                $rules[] = 'max:255';
                break;
            case 'textarea':
                $rules[] = 'string';
                $rules[] = 'max:1000';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'email':
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'select':
                $rules[] = 'in:' . implode(',', $this->options ?? []);
                break;
            case 'multiselect':
                $rules[] = 'array';
                if ($this->options) {
                    $rules[] = 'in:' . implode(',', $this->options);
                }
                break;
            case 'boolean':
                $rules[] = 'boolean';
                break;
        }

        // Add custom validation rules
        if ($this->validation_rules) {
            $customRules = explode('|', $this->validation_rules);
            $rules = array_merge($rules, $customRules);
        }

        return $rules;
    }

    /**
     * Get field options for forms
     */
    public function getFormOptions(): array
    {
        return [
            'label' => $this->name,
            'required' => $this->is_required,
            'help' => $this->help_text,
            'default' => $this->default_value,
        ];
    }

    /**
     * Generate slug from name
     */
    public static function generateSlug(string $name): string
    {
        return Str::slug($name, '_');
    }

    /**
     * Check if field is of specific type
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * Get options for select/multiselect fields
     */
    public function getSelectOptions(): array
    {
        if (!$this->options) {
            return [];
        }

        $options = [];
        foreach ($this->options as $option) {
            $options[$option] = $option;
        }

        return $options;
    }
}