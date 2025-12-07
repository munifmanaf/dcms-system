<?php
// app/Http/Controllers/ImageController.php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\SimpleImageProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    protected $imageProcessor;
    
    public function __construct(SimpleImageProcessor $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
        $this->middleware('auth')->except(['index', 'show', 'publicGallery']);
    }
    
    /**
     * Display user's image gallery
     */
    // In your index() method:
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        // Get stats
        $totalImages = Image::where('user_id', $userId)->count();
        $publicImages = Image::where('user_id', $userId)->where('is_public', true)->count();
        $totalSize = Image::where('user_id', $userId)->sum('size');
        $categories = Image::where('user_id', $userId)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();
        
        // Query images with filters
        $query = Image::where('user_id', $userId)
            ->with('item');
        
        // Apply filters...
        
        $images = $query->latest()->paginate($request->per_page ?? 24);
        
        return view('images.index', compact(
            'images', 
            'categories', 
            'totalImages',
            'publicImages',
            'totalSize'
        ));
    }
        
    /**
     * Show public gallery
     */
    public function publicGallery()
    {
        $images = Image::where('is_public', true)
            ->with('user')
            ->latest()
            ->paginate(24);
            
        return view('images.public', compact('images'));
    }
    
    /**
     * Show upload form
     */
    public function create()
    {
        return view('images.create');
    }
    
    /**
     * Store uploaded image
     */
    public function store(Request $request)
    {
        try {
            // Process the image
            $result = $this->imageProcessor->uploadFile(
                $request->file('image'),
                auth()->id(),
                $request->item_id
            );
            
            // Check if upload was successful
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Upload failed'
                ], 500);
            }
            
            // Save to database
            $image = Image::create([
                'user_id' => auth()->id(),
                'item_id' => $request->item_id,
                'original_name' => $result['original_name'],
                'stored_name' => $result['stored_name'],
                'path' => $result['path'],
                'previews' => $result['previews'] ?? [],
                'metadata' => $result['metadata'] ?? [],
                'extension' => $result['extension'],
                'size' => $result['size'],
                'mime_type' => $result['mime_type'],
                'width' => $result['metadata']['dimensions']['width'] ?? null,
                'height' => $result['metadata']['dimensions']['height'] ?? null,
                'category' => $request->category,
                'description' => $request->description,
                'tags' => $request->tags,
                'is_public' => $request->boolean('is_public', false),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image' => [
                    'id' => $image->id,
                    'name' => $image->original_name,
                    'thumbnail_url' => $image->thumbnail_url,
                    'preview_url' => $image->preview_url,
                    'url' => $image->url,
                    'size' => $image->formatted_size,
                    'dimensions' => $image->dimensions,
                    'path' => $image->path, // Debug info
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Image upload failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display single image
     */
    public function show(Image $image)
    {
        // Check permissions
        if ($image->user_id !== auth()->id() && !$image->is_public) {
            abort(403, 'This image is private');
        }
        
        return view('images.show', compact('image'));
    }
    
    /**
     * Show edit form
     */
    public function edit(Image $image)
    {
        $this->authorize('update', $image);
        
        return view('images.edit', compact('image'));
    }
    
    /**
     * Update image details
     */
    public function update(Request $request, Image $image)
    {
        $this->authorize('update', $image);
        
        $validated = $request->validate([
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|string|max:255',
            'is_public' => 'boolean',
        ]);
        
        $image->update($validated);
        
        return redirect()->route('images.show', $image)
            ->with('success', 'Image updated successfully');
    }
    
    /**
     * Delete image
     */
    public function destroy(Image $image)
    {
        $this->authorize('delete', $image);
        
        try {
            // Delete all files
            Storage::disk('public')->delete($image->path);
            
            // Delete previews
            $previews = $image->previews ?? [];
            foreach ($previews as $preview) {
                if (isset($preview['path'])) {
                    Storage::disk('public')->delete($preview['path']);
                }
            }
            
            $image->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image'
            ], 500);
        }
    }
    
    /**
     * Batch upload for multiple images
     */
    public function batchUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|max:50000',
            'category' => 'nullable|string|max:100',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $uploaded = [];
        $failed = [];
        
        foreach ($request->file('images') as $file) {
            try {
                $result = $this->imageProcessor->uploadFile(
                    $file,
                    auth()->id(),
                    $request->item_id
                );
                
                if ($result['success']) {
                    $image = Image::create([
                        'user_id' => auth()->id(),
                        'item_id' => $request->item_id,
                        'original_name' => $result['original_name'],
                        'stored_name' => $result['stored_name'],
                        'path' => $result['path'],
                        'previews' => $result['previews'],
                        'metadata' => $result['metadata'],
                        'extension' => $result['extension'],
                        'size' => $result['size'],
                        'width' => $result['metadata']['dimensions']['width'] ?? null,
                        'height' => $result['metadata']['dimensions']['height'] ?? null,
                        'category' => $request->category,
                        'is_public' => $request->boolean('is_public', false),
                    ]);
                    
                    $uploaded[] = $image->original_name;
                } else {
                    $failed[] = $file->getClientOriginalName();
                }
                
            } catch (\Exception $e) {
                $failed[] = $file->getClientOriginalName();
            }
        }
        
        $message = '';
        if (count($uploaded) > 0) {
            $message .= 'Uploaded: ' . implode(', ', $uploaded) . '. ';
        }
        if (count($failed) > 0) {
            $message .= 'Failed: ' . implode(', ', $failed);
        }
        
        return redirect()->route('images.index')
            ->with('success', $message ?: 'No images uploaded');
    }
    
    /**
     * Serve image in specific size
     */
    public function serve($id, $size = 'original')
    {
        $image = Image::findOrFail($id);
        
        // Check permissions
        if ($image->user_id !== auth()->id() && !$image->is_public) {
            abort(403);
        }
        
        $path = $image->path;
        
        if ($size !== 'original' && isset($image->previews[$size])) {
            $path = $image->previews[$size]['path'];
        }
        
        $fullPath = Storage::disk('public')->path($path);
        
        if (!file_exists($fullPath)) {
            abort(404);
        }
        
        return response()->file($fullPath);
    }
    
    /**
     * Download original image
     */
    public function download(Image $image)
    {
        // Check permissions
        if ($image->user_id !== auth()->id() && !$image->is_public) {
            abort(403);
        }
        
        $path = Storage::disk('public')->path($image->path);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        return response()->download($path, $image->original_name);
    }
}