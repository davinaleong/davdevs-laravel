<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(config('services.cloudinary.url'));
    }

    public function upload(UploadedFile $file, bool $stripExif = true): array
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => 'davdevs',
            'resource_type' => 'image',
            // Cloudinary strips EXIF/metadata from the delivered asset by default;
            // this explicitly keeps only color-profile data needed for correct rendering.
            'image_metadata' => false,
        ]);

        return [
            'cloudinary_id' => $result['public_id'],
            'url' => $result['secure_url'],
            'width' => $result['width'] ?? null,
            'height' => $result['height'] ?? null,
            'format' => $result['format'] ?? null,
            'bytes' => $result['bytes'] ?? null,
        ];
    }

    public function destroy(string $publicId): void
    {
        $this->cloudinary->uploadApi()->destroy($publicId);
    }

    public function transformedUrl(string $publicId, array $options = []): string
    {
        $transformation = array_merge([
            'quality' => 'auto',
            'fetch_format' => 'auto',
        ], $options);

        return $this->cloudinary->image($publicId)
            ->resize(Resize::scale()
                ->width($transformation['width'] ?? null))
            ->toUrl();
    }
}
