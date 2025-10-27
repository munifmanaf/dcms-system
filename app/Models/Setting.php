<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'value', 'type', 'group', 'description'
    ];

    protected $casts = [
        'value' => 'string' // We'll handle casting manually
    ];

    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? static::castValue($setting->value, $setting->type) : $default;
    }

    public static function setValue($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->type = $type;
        $setting->group = $group;
        $setting->description = $description;
        $setting->save();
        return $setting;
    }

    protected static function castValue($value, $type)
    {
        return match($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => (string) $value,
        };
    }
}