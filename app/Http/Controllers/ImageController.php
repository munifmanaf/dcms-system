<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\ImageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    protected $imageService;
    
    public function __construct(ImageProcessingService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth')->except(['show', 'thumbnail']);
    }
    
    /**
     * Display a listing of images
     */
    public function index(Request $request)
    {
        $images = Image::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
            
        return view('images.index', compact('images'));
    }
    
    /**
     * Show the form for uploading images
     */
    public function create()
    {
        return view('images.create');
    }
    
    /**
     * Store a newly uploaded image
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'add_watermark' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $imageData = $this->imageService->processUploadedImage(
                $request->file('image'),
                auth()->id()
            );
            dd($imageData);
            // Add watermark if requested
            if ($request->boolean('add_watermark')) {
                $this->imageService->addWatermark($imageData['paths']['original']);
                $imageData['has_watermark'] = true;
            }
            
            // Extract metadata
            $metadata = $this->imageService->extractExifData($imageData['paths']['original']);
            
            // Create image record
            $image = Image::create([
                'user_id' => auth()->id(),
                'original_name' => $imageData['original_name'],
                'stored_name' => $imageData['stored_name'],
                'paths' => $imageData['paths'],
                'mime_type' => $imageData['mime_type'],
                'size' => $imageData['size'],
                'metadata' => $metadata,
                'is_optimized' => true,
                'has_watermark' => $request->boolean('add_watermark', false),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image' => $image,
                'thumbnail_url' => $image->getThumbnailUrl(),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified image
     */
    public function show(Image $image)
    {
        return view('images.show', compact('image'));
    }
    
    /**
     * Show the form for editing image details
     */
    public function edit(Image $image)
    {
        $this->authorize('update', $image);
        
        return view('images.edit', compact('image'));
    }
    
    /**
     * Update image metadata
     */
    public function update(Request $request, Image $image)
    {
        $this->authorize('update', $image);
        
        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $image->update($validated);
        
        return redirect()->route('images.index')
            ->with('success', 'Image updated successfully');
    }
    
    /**
     * Remove the specified image
     */
    public function destroy(Image $image)
    {
        $this->authorize('delete', $image);
        
        // Delete all image files
        foreach ($image->paths as $path) {
            Storage::disk('public')->delete($path);
        }
        
        $image->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }
    
    /**
     * Batch image processing
     */
    public function batchProcess(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'action' => 'required|in:resize,watermark,optimize',
            'width' => 'required_if:action,resize|integer|min:50|max:4000',
            'height' => 'required_if:action,resize|integer|min:50|max:4000',
            'watermark_text' => 'required_if:action,watermark|string|max:100',
        ]);
        
        $images = Image::whereIn('id', $request->image_ids)
            ->where('user_id', auth()->id())
            ->get();
            
        $results = [];
        
        foreach ($images as $image) {
            switch ($request->action) {
                case 'resize':
                    $newPath = $this->imageService->resizeSingleImage(
                        $image->getPath('original'),
                        $request->width,
                        $request->height,
                        $request->boolean('keep_aspect_ratio', true)
                    );
                    
                    // Update paths in database
                    $paths = $image->paths;
                    $paths['custom_resized'] = $newPath;
                    $image->update(['paths' => $paths]);
                    
                    $results[$image->id] = [
                        'success' => true,
                        'new_path' => $newPath
                    ];
                    break;
                    
                case 'watermark':
                    $this->imageService->addWatermark(
                        $image->getPath('original'),
                        $request->watermark_text
                    );
                    
                    $image->update(['has_watermark' => true]);
                    $results[$image->id] = ['success' => true];
                    break;
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
    
    /**
     * Get thumbnail image
     */
    public function thumbnail($id)
    {
        $image = Image::findOrFail($id);
        
        // Check if thumbnail exists
        $thumbnailPath = Storage::disk('public')->path($image->getPath('thumbnail'));
        
        if (!file_exists($thumbnailPath)) {
            abort(404);
        }
        
        return response()->file($thumbnailPath);
    }
}