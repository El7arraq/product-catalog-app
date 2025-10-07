<?php
namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadService implements FileUploadServiceInterface
{
    private Filesystem $storage;

    public function __construct(Filesystem $storage)
    {
        $this->storage = $storage;
    }

    public function uploadImage(UploadedFile $file, string $directory = 'uploads'): string
    {
        $this->validateImageFile($file);
        
        $filename = $this->generateUniqueFilename($file);
        
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }

    public function deleteImage(string $path): bool
    {
        if ($this->storage->exists($path)) {
            return $this->storage->delete($path);
        }
        
        return false;
    }

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

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException('Image file size must not exceed 5MB.');
        }
    }

    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $timestamp = now()->format('Y-m-d-H-i-s');
        $random = Str::random(8);
        
        return "{$name}-{$timestamp}-{$random}.{$extension}";
    }
}