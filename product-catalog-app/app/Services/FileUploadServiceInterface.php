<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

interface FileUploadServiceInterface
{
    public function uploadImage(UploadedFile $file, string $directory = 'uploads'): string;
    public function deleteImage(string $path): bool;
}