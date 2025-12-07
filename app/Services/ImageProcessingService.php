<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageProcessingService
{
    protected $manager;
    
    public function __construct()
    {
        // Choose driver based on what's available
        if (extension_loaded('imagick')) {
            $this->manager = new ImageManager(new ImagickDriver());
            Log::info('Using Imagick driver for image processing');
        } else {
            $this->manager = new ImageManager(new GdDriver());
            Log::info('Using GD driver for image processing');
        }
    }
    
    /**
     * Process uploaded image with multiple versions
     */
    public function processUploadedImage(UploadedFile $file, $userId, $folder = 'images')
    {
        $filename = $this->generateFilename($file);
        $directory = "{$folder}/user_{$userId}/" . now()->format('Y/m/d');
        
        // Create directory if not exists
        Storage::disk('public')->makeDirectory($directory);
        
        // Original image path
        $originalPath = "{$directory}/{$filename}";
        
        // Store original file
        $file->storeAs($directory, $filename, 'public');
        
        try {
            // Create different sizes
            $paths = [
                'original' => $originalPath,
                'thumbnail' => $this->createThumbnail($file, $directory, $filename),
                'medium' => $this->createMediumSize($file, $directory, $filename),
                'large' => $this->createLargeSize($file, $directory, $filename),
            ];
            
            // Remove null values (if any resizing failed)
            $paths = array_filter($paths);
            
            return [
                'success' => true,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $filename,
                'paths' => $paths,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
            
        } catch (\Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'user_id' => $userId
            ]);
            
            // Return at least the original
            return [
                'success' => false,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $filename,
                'paths' => ['original' => $originalPath],
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Create thumbnail (150x150)
     */
    protected function createThumbnail($file, $directory, $filename)
    {
        try {
            $thumbPath = "{$directory}/thumb_{$filename}";
            $fullPath = storage_path("app/public/{$thumbPath}");
            
            // Read and process image
            $image = $this->manager->read($file->getRealPath());
            
            // Resize to thumbnail
            $image->cover(150, 150);
            
            // Save with quality
            $image->save($fullPath, quality: 80);
            
            return $thumbPath;
            
        } catch (\Exception $e) {
            Log::warning('Thumbnail creation failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create medium size (800x600)
     */
    protected function createMediumSize($file, $directory, $filename)
    {
        try {
            $mediumPath = "{$directory}/medium_{$filename}";
            $fullPath = storage_path("app/public/{$mediumPath}");
            
            // Read fresh copy
            $image = $this->manager->read($file->getRealPath());
            
            // Resize with aspect ratio
            $image->scale(800, 600);
            
            // Save
            $image->save($fullPath, quality: 85);
            
            return $mediumPath;
            
        } catch (\Exception $e) {
            Log::warning('Medium size creation failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create large size (1200x900)
     */
    protected function createLargeSize($file, $directory, $filename)
    {
        try {
            $largePath = "{$directory}/large_{$filename}";
            $fullPath = storage_path("app/public/{$largePath}");
            
            // Read fresh copy
            $image = $this->manager->read($file->getRealPath());
            
            // Scale down only if larger
            $image->scaleDown(1200, 900);
            
            // Save
            $image->save($fullPath, quality: 90);
            
            return $largePath;
            
        } catch (\Exception $e) {
            Log::warning('Large size creation failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Add watermark to image
     */
    public function addWatermark($imagePath, $text = null, $position = 'bottom-right')
    {
        try {
            $fullPath = Storage::disk('public')->path($imagePath);
            
            // Read image
            $image = $this->manager->read($fullPath);
            
            $watermarkText = $text ?? config('app.name', 'DCMS');
            
            // Get image dimensions
            $width = $image->width();
            $height = $image->height();
            
            // Add text watermark
            $image->text($watermarkText, function($font) use ($width, $height, $position) {
                $font->filename($this->getFontPath());
                $font->size(24);
                $font->color([255, 255, 255, 0.6]); // White with 60% opacity
                
                // Set position
                switch ($position) {
                    case 'bottom-right':
                        $font->position($width - 20, $height - 20);
                        $font->align('right');
                        $font->valign('bottom');
                        break;
                    case 'center':
                        $font->position($width / 2, $height / 2);
                        $font->align('center');
                        $font->valign('middle');
                        break;
                    default:
                        $font->position(20, 20);
                        $font->align('left');
                        $font->valign('top');
                }
            });
            
            // Save with watermark
            $image->save($fullPath);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Watermark failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Resize single image
     */
    public function resizeSingleImage($imagePath, $width, $height, $keepAspectRatio = true)
    {
        try {
            $fullPath = Storage::disk('public')->path($imagePath);
            $newPath = preg_replace('/(\.[^\.]+)$/', '_resized$1', $imagePath);
            $newFullPath = Storage::disk('public')->path($newPath);
            
            $image = $this->manager->read($fullPath);
            
            if ($keepAspectRatio) {
                $image->scale($width, $height);
            } else {
                $image->cover($width, $height);
            }
            
            $image->save($newFullPath);
            
            return $newPath;
            
        } catch (\Exception $e) {
            Log::error('Image resize failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get image dimensions and info
     */
    public function getImageInfo($imagePath)
    {
        try {
            $fullPath = Storage::disk('public')->path($imagePath);
            
            if (!file_exists($fullPath)) {
                return null;
            }
            
            $image = $this->manager->read($fullPath);
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'size' => filesize($fullPath),
                'modified' => filemtime($fullPath),
            ];
            
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    /**
     * Get font path for watermark
     */
    protected function getFontPath()
    {
        $fontPath = resource_path('fonts/Roboto-Regular.ttf');
        
        if (!file_exists($fontPath)) {
            // Fallback to a system font or skip
            Log::warning('Font file not found: ' . $fontPath);
            
            // Try to find a system font
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows font path
                $fontPath = 'C:\Windows\Fonts\arial.ttf';
            } else {
                // Linux/Mac font path
                $fontPath = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
            }
        }
        
        return $fontPath;
    }
}