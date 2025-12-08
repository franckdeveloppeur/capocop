<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Media;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    // temporary holder for uploaded product images from the form
    protected $product_images;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove product_images from the product data as it should be handled separately
        $this->product_images = $data['product_images'] ?? null;
        unset($data['product_images']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Handle product images and create media records
        if (isset($this->product_images) && !empty($this->product_images)) {
            $imageFiles = $this->product_images;
            
            // Ensure it's an array
            if (!is_array($imageFiles)) {
                $imageFiles = [$imageFiles];
            }

            foreach ($imageFiles as $filePath) {
                if ($filePath) {
                    // Get file info from the disk if available; be defensive because Flysystem
                    // may not be able to retrieve metadata for some uploaded temp files.
                    $disk = Storage::disk('public');
                    $normalizedPath = ltrim($filePath, '/');
                    $fileSize = null;
                    $mimeType = null;
                    $diskName = 'public';

                    try {
                        if ($disk->exists($normalizedPath)) {
                            $fileSize = $disk->size($normalizedPath);
                        } elseif (file_exists($filePath)) {
                            // Fallback to local filesystem path (tmp) if present
                            $fileSize = filesize($filePath);
                            $mimeType = mime_content_type($filePath) ?: null;
                            $diskName = 'local';
                        }
                    } catch (\Throwable $e) {
                        // If metadata retrieval fails, continue and store what we have
                    }

                    $originalFileName = basename($filePath);

                    // Create a unique filename for storage to avoid collisions
                    $ext = pathinfo($originalFileName, PATHINFO_EXTENSION);
                    $uniqueName = (string) Str::uuid() . ($ext ? '.' . $ext : '');
                    $targetPath = 'products/' . $uniqueName;

                    try {
                        // If file exists on the target disk, move/rename to unique path
                        if ($disk->exists($normalizedPath)) {
                            if ($normalizedPath !== $targetPath) {
                                // Attempt to move the file to the unique name
                                try {
                                    $disk->move($normalizedPath, $targetPath);
                                    $filePath = $targetPath;
                                } catch (\Throwable $e) {
                                    // If move fails, attempt to copy contents instead
                                    $contents = $disk->get($normalizedPath);
                                    if ($contents !== false) {
                                        $disk->put($targetPath, $contents);
                                        $filePath = $targetPath;
                                    }
                                }
                            } else {
                                $filePath = $targetPath;
                            }
                        } elseif (file_exists($filePath)) {
                            // Local temporary uploaded file: copy to public disk
                            try {
                                $contents = file_get_contents($filePath);
                                Storage::disk('public')->put($targetPath, $contents);
                                $filePath = $targetPath;
                            } catch (\Throwable $e) {
                                // ignore and continue; we will still create media record with original path
                            }
                        }
                    } catch (\Throwable $e) {
                        // ignore filesystem move/copy failures
                    }

                    $storedFileName = basename($filePath);

                    // Ensure mime type is never null to satisfy DB NOT NULL constraint
                    $mimeType = $mimeType ?? $this->guessMimeType($storedFileName);

                    // Create media record (store 0 when size missing)
                    Media::create([
                        'model_type' => \App\Models\Product::class,
                        'model_id' => $this->record->id,
                        'collection_name' => 'images',
                        'file_name' => $storedFileName,
                        'mime_type' => $mimeType,
                        'disk' => $diskName,
                        'size' => $fileSize ?? 0,
                        'custom_properties' => ['full_path' => $filePath],
                        'order_column' => 1,
                    ]);
                }
            }
        }
    }

    protected function guessMimeType(string $fileName): string
    {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            default => 'application/octet-stream',
        };
    }
}
