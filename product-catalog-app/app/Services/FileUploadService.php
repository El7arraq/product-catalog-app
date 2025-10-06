<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService implements FileUploadServiceInterface
{
    /**
     * Upload an image file and return the stored path.
     */
    public function uploadImage(UploadedFile $file, string $directory = 'uploads'): string
    {
        // Validate file type
        $this->validateImageFile($file);
        
        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);
        
        // Store file in public disk
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }

    /**
     * Delete an image file.
     */
    public function deleteImage(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    /**
     * Validate that the uploaded file is an image.
     */
    private function validateImageFile(UploadedFile $file): void
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \InvalidArgumentException('File must be an image (JPEG, PNG, GIF, or WebP).');
        }

        // Check file size (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException('Image file size must not exceed 5MB.');
        }
    }

    /**
     * Generate a unique filename for the uploaded file.
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $timestamp = now()->format('Y-m-d-H-i-s');
        $random = Str::random(8);
        
        return "{$name}-{$timestamp}-{$random}.{$extension}";
    }
}