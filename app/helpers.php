<?php

// app/helpers.php

use App\Models\Item;

if (!function_exists('get_metadata_value')) {
    /**
     * Safely get metadata value from item or old input
     * Always returns a string for form inputs
     */
    function get_metadata_value($item, $key, $default = '') {
        // First check for old input (form validation errors)
        $oldValue = old("metadata.{$key}");
        if ($oldValue !== null) {
            return is_string($oldValue) ? $oldValue : (is_array($oldValue) ? json_encode($oldValue) : strval($oldValue));
        }
        
        // Then check existing item data
        if (isset($item) && $item->metadata) {
            $metadata = $item->metadata;
            
            // Ensure metadata is an array
            if (is_string($metadata)) {
                $metadata = json_decode($metadata, true) ?? [];
            }
            
            $value = $metadata[$key] ?? $default;
            
            // Convert arrays and objects to JSON strings for display
            if (is_array($value) || is_object($value)) {
                return json_encode($value);
            }
            
            return strval($value);
        }
        
        return $default;
    }
}

if (!function_exists('get_simple_metadata_value')) {
    /**
     * Get simple metadata value (only strings/numbers, no arrays)
     */
    function get_simple_metadata_value($item, $key, $default = '') {
        $value = get_metadata_value($item, $key, $default);
        
        // If value is a JSON string representing an array, return empty
        if (is_string($value) && preg_match('/^\[.*\]$|^\{.*\}$/', $value)) {
            return $default;
        }
        
        return $value;
    }
}

if (!function_exists('get_custom_metadata')) {
    /**
     * Get custom metadata fields (excluding predefined ones)
     * Only returns simple key-value pairs (no nested arrays)
     */
    function get_custom_metadata($item) {
        $predefined = ['author', 'year', 'language', 'pages'];
        $metadata = [];
        
        // Handle old input first
        if (old('metadata_keys') && old('metadata_values')) {
            foreach (old('metadata_keys') as $index => $key) {
                $value = old('metadata_values')[$index] ?? '';
                if (!empty($key) && !empty($value) && is_string($value)) {
                    $metadata[$key] = $value;
                }
            }
            return $metadata;
        }
        
        // Handle existing item data
        if (isset($item) && $item->metadata) {
            $itemMetadata = $item->metadata;
            
            // Ensure metadata is an array
            if (is_string($itemMetadata)) {
                $itemMetadata = json_decode($itemMetadata, true) ?? [];
            }
            
            if (is_array($itemMetadata)) {
                foreach ($itemMetadata as $key => $value) {
                    // Skip predefined fields
                    if (in_array($key, $predefined)) {
                        continue;
                    }
                    
                    // Only include simple string/number values (no arrays/objects)
                    if (!is_array($value) && !is_object($value) && !empty($key) && $value !== '') {
                        $metadata[$key] = strval($value);
                    }
                }
            }
        }
        
        return $metadata;
    }
}

if (!function_exists('is_metadata_value_displayable')) {
    /**
     * Check if a metadata value can be displayed in a form input
     */
    function is_metadata_value_displayable($value) {
        return !is_array($value) && !is_object($value) && $value !== null && $value !== '';
    }
}