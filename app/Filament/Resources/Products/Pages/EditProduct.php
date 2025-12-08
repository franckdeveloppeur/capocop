<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Media;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
    // temporary holder for uploaded product images from the form
    protected $product_images;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove product_images from the product data as it should be handled separately
        $this->product_images = $data['product_images'] ?? null;
        unset($data['product_images']);
        
        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // When opening the edit form, prefill the product_images field with the
        // first media path so the FileUpload shows the current image.
        try {
            $firstMedia = $this->record->media()->first();
            if ($firstMedia) {
                $data['product_images'] = $firstMedia->custom_properties['full_path'] ?? ('products/' . $firstMedia->file_name);
            }
        } catch (\Throwable $e) {
            // ignore; leave data as-is
        }

        return $data;
    }

    protected function afterSave(): void
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
                    // Check if media already exists for this file
                    $mediaExists = Media::where('model_type', \App\Models\Product::class)
                        ->where('model_id', $this->record->id)
                        ->where('file_name', basename($filePath))
                        ->exists();

                    if (!$mediaExists) {
                        // Get file info from the disk if available; be defensive
                        $disk = Storage::disk('public');
                        $normalizedPath = ltrim($filePath, '/');
                        $fileSize = null;
                        $mimeType = null;
                        $diskName = 'public';

                        try {
                            if ($disk->exists($normalizedPath)) {
                                    $fileSize = $disk->size($normalizedPath);
                                } elseif (file_exists($filePath)) {
                                    $fileSize = filesize($filePath);
                                    $mimeType = mime_content_type($filePath) ?: null;
                                    $diskName = 'local';
                                }
                        } catch (\Throwable $e) {
                            // ignore metadata errors
                        }

                            $originalFileName = basename($filePath);
                            $ext = pathinfo($originalFileName, PATHINFO_EXTENSION);
                            $uniqueName = (string) Str::uuid() . ($ext ? '.' . $ext : '');
                            $targetPath = 'products/' . $uniqueName;

                            try {
                                if ($disk->exists($normalizedPath)) {
                                    if ($normalizedPath !== $targetPath) {
                                        try {
                                            $disk->move($normalizedPath, $targetPath);
                                            $filePath = $targetPath;
                                        } catch (\Throwable $e) {
                                            // fallback: read & write
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
                                    try {
                                        $contents = file_get_contents($filePath);
                                        Storage::disk('public')->put($targetPath, $contents);
                                        $filePath = $targetPath;
                                    } catch (\Throwable $e) {
                                        // ignore
                                    }
                                }
                            } catch (\Throwable $e) {
                                // ignore
                            }

                            $storedFileName = basename($filePath);
                            $mimeType = $mimeType ?? $this->guessMimeType($storedFileName);

                        // Create media record
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
