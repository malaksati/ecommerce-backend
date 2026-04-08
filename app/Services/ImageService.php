<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

class ImageService
{
    /**
     * Upload and resize a product image.
     *
     * @param UploadedFile $file
     * @return string|null  Path to stored image or null on failure
     */
    public function uploadProductImage(UploadedFile $file): ?string
    {
        try {
            // Validate file type
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                throw new Exception('Invalid image type. Only JPEG, PNG, and WebP are allowed.');
            }

            // Generate unique filename with original extension
            $extension = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
            $filename = Str::uuid() . '.' . $extension;

            // Read and resize image
            $manager = new ImageManager(Driver::class);

            $image = $manager->decodePath($file->getRealPath())
                ->scaleDown(800, 800)  // respects aspect ratio, never upscales
                ->encode(new AutoEncoder(quality: 80));

            // Define storage path
            $path = "products/{$filename}";

            // Store in public disk
            Storage::disk('public')->put($path, (string) $image);

            return $path;
        } catch (Exception $e) {
            // Log error for debugging
            Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }
    // public function uploadProductImage($file)
    // {
    //     $filename = Str::uuid() . '.jpg';

    //     $image = Image::read($file)
    //         ->resize(800, 800) // standard size
    //         ->toJpeg(80);

    //     $path = "products/{$filename}";

    //     Storage::disk('public')->put($path, (string) $image);

    //     return $path;
    // }
}
