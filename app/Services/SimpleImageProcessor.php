<?php
// app/Services/SimpleImageProcessor.php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class SimpleImageProcessor
{
    /**
     * Upload file using YOUR working method
     */
    public function uploadFile(UploadedFile $file, $userId, $itemId = null)
    {
        Log::info('=== IMAGE PROCESSOR START ===');
        
        try {
            // 1. Validate file is valid
            if (!$file->isValid()) {
                throw new \Exception('Uploaded file is not valid');
            }
            
            // 2. Get file info
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            
            Log::info('File info:', [
                'name' => $originalName,
                'extension' => $extension,
                'mime' => $mimeType,
                'size' => $file->getSize(),
                'temp_path' => $file->getPathname(),
                'temp_exists' => file_exists($file->getPathname())
            ]);
            
            // 3. Sanitize filename (like your method)
            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $fileName = time() . '_' . $safeName;
            
            // 4. Create directory path (like your items/ structure)
            $directory = "images/user_{$userId}/" . date('Y/m/d');
            $filePath = "{$directory}/{$fileName}";
            
            Log::info('Generated paths:', [
                'safe_name' => $safeName,
                'file_name' => $fileName,
                'directory' => $directory,
                'file_path' => $filePath
            ]);
            
            // 5. Create directory if needed
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            
            // 6. Store file using YOUR WORKING METHOD
            $fileContent = file_get_contents($file->getPathname());
            
            if ($fileContent === false) {
                throw new \Exception('Could not read file content');
            }
            
            // Store the file
            Storage::disk('public')->put($filePath, $fileContent);
            
            Log::info('File stored:', ['path' => $filePath]);
            
            // 7. Verify file was stored
            if (!Storage::disk('public')->exists($filePath)) {
                throw new \Exception('File storage failed: ' . $filePath);
            }
            
            // 8. Get actual stored file info
            $storedFileSize = Storage::disk('public')->size($filePath);
            $storedMimeType = Storage::disk('public')->mimeType($filePath);
            $fullPath = Storage::disk('public')->path($filePath);
            
            Log::info('Stored file verified:', [
                'stored_size' => $storedFileSize,
                'stored_mime' => $storedMimeType,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath)
            ]);
            
            // 9. Create previews if it's an image
            $previews = [];
            $metadata = [];
            
            if ($this->isImage($extension)) {
                // Create previews
                $previews = $this->createPreviews($fullPath, $directory, $fileName);
                
                // Extract metadata
                $metadata = $this->extractMetadata($fullPath);
                
                // Get dimensions
                if ($info = @getimagesize($fullPath)) {
                    $metadata['dimensions'] = [
                        'width' => $info[0],
                        'height' => $info[1],
                        'mime' => $info['mime']
                    ];
                }
            }
            
            // 10. Return success
            return [
                'success' => true,
                'original_name' => $originalName,
                'stored_name' => $fileName,
                'path' => $filePath,
                'url' => Storage::url($filePath),
                'previews' => $previews,
                'metadata' => $metadata,
                'extension' => $extension,
                'size' => $storedFileSize,
                'mime_type' => $storedMimeType,
            ];
            
        } catch (\Exception $e) {
            Log::error('ImageProcessor Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'original_name' => $file->getClientOriginalName() ?? 'Unknown',
            ];
        }
    }
    
    /**
     * Create previews for image
     */
    protected function createPreviews($sourcePath, $directory, $filename)
    {
        $previews = [];
        
        // Only create previews if GD is available
        if (!function_exists('gd_info')) {
            Log::warning('GD library not available - skipping previews');
            return $previews;
        }
        
        // Preview sizes
        $sizes = [
            'thm' => ['width' => 150, 'height' => 150],   // Thumbnail
            'pre' => ['width' => 300, 'height' => 300],   // Preview
        ];
        
        foreach ($sizes as $sizeName => $dimensions) {
            try {
                $previewPath = $this->createPreviewImage(
                    $sourcePath, 
                    $directory, 
                    $filename, 
                    $sizeName, 
                    $dimensions['width'], 
                    $dimensions['height']
                );
                
                if ($previewPath) {
                    $previews[$sizeName] = [
                        'path' => $previewPath,
                        'url' => Storage::url($previewPath),
                        'width' => $dimensions['width'],
                        'height' => $dimensions['height'],
                    ];
                    
                    Log::info("Preview created: {$sizeName}", ['path' => $previewPath]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to create {$sizeName} preview:", ['error' => $e->getMessage()]);
                // Continue with other sizes
            }
        }
        
        return $previews;
    }
    
    /**
     * Create single preview image
     */
    protected function createPreviewImage($sourcePath, $directory, $filename, $sizeName, $width, $height)
    {
        // Check if source exists
        if (!file_exists($sourcePath)) {
            throw new \Exception("Source file not found: {$sourcePath}");
        }
        
        // Get image info
        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new \Exception("Invalid image file");
        }
        
        list($srcWidth, $srcHeight, $type) = $imageInfo;
        
        // Load source image
        $source = $this->loadImage($sourcePath, $type);
        if (!$source) {
            throw new \Exception("Failed to load image (type: {$type})");
        }
        
        // Calculate new dimensions with aspect ratio
        $ratio = min($width/$srcWidth, $height/$srcHeight);
        $newWidth = round($srcWidth * $ratio);
        $newHeight = round($srcHeight * $ratio);
        
        // Create preview image
        $preview = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG/GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagecolortransparent($preview, imagecolorallocatealpha($preview, 0, 0, 0, 127));
            imagealphablending($preview, false);
            imagesavealpha($preview, true);
        }
        
        // Resize
        imagecopyresampled($preview, $source, 0, 0, 0, 0, 
                          $newWidth, $newHeight, $srcWidth, $srcHeight);
        
        // Generate preview filename and path
        $previewFilename = "{$sizeName}_{$filename}";
        $previewPath = "{$directory}/{$previewFilename}";
        $targetPath = Storage::disk('public')->path($previewPath);
        
        // Ensure directory exists
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Save preview
        $this->saveImage($preview, $targetPath, $type);
        
        // Clean up
        imagedestroy($source);
        imagedestroy($preview);
        
        return $previewPath;
    }
    
    /**
     * Load image from file
     */
    protected function loadImage($path, $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    return imagecreatefromwebp($path);
                }
                return null;
            default:
                return null;
        }
    }
    
    /**
     * Save image to file
     */
    protected function saveImage($image, $path, $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagejpeg($image, $path, 85);
            case IMAGETYPE_PNG:
                return imagepng($image, $path, 8);
            case IMAGETYPE_GIF:
                return imagegif($image, $path);
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    return imagewebp($image, $path, 85);
                }
                return false;
            default:
                return false;
        }
    }
    
    /**
     * Extract metadata from image
     */
    protected function extractMetadata($path)
    {
        $metadata = [
            'filesize' => filesize($path),
            'modified' => date('Y-m-d H:i:s', filemtime($path)),
        ];
        
        // Try to get EXIF data
        if (function_exists('exif_read_data')) {
            try {
                $exif = @exif_read_data($path, 0, true);
                if ($exif) {
                    // Camera model
                    if (isset($exif['IFD0']['Model'])) {
                        $metadata['camera'] = trim($exif['IFD0']['Model']);
                    }
                    // Date taken
                    if (isset($exif['EXIF']['DateTimeOriginal'])) {
                        $metadata['date_taken'] = $exif['EXIF']['DateTimeOriginal'];
                    }
                    // GPS data
                    if (isset($exif['GPS'])) {
                        $metadata['gps'] = $exif['GPS'];
                    }
                }
            } catch (\Exception $e) {
                // Ignore EXIF errors
            }
        }
        
        return $metadata;
    }
    
    /**
     * Check if file is an image
     */
    protected function isImage($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        return in_array($extension, $imageExtensions);
    }
}