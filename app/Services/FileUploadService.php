<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function storeFile(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
        return $file->storeAs($directory, $fileName, $disk);
    }

    public function deleteFile(string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($disk)->exists($path) && Storage::disk($disk)->delete($path);
    }

    public function fileExists(string $path, string $disk = 'public'): bool
    {
        return ! empty($path) && Storage::disk($disk)->exists($path);
    }

    public function getUrl(string $path, string $disk = 'public'): ?string
    {
        if ($this->fileExists($path, $disk)) {
            return Storage::disk($disk)->url($path);
        }

        return null;
    }
}
