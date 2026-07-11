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

    /**
     * @param  string|null  $publicId  Slash-delimited path (relative to the `davdevs` root, e.g.
     *                                 `entries/article/{slug}/01-name`) that becomes the asset's
     *                                 Cloudinary public ID, making it identifiable by path alone
     *                                 in the Cloudinary console. Omit for an auto-generated ID
     *                                 under the flat `davdevs` folder (the CMS Image Manager's
     *                                 standalone upload flow, which has no owning entry yet).
     */
    public function upload(UploadedFile $file, bool $stripExif = true, ?string $publicId = null): array
    {
        $isSvg = in_array($file->getMimeType(), ['image/svg+xml', 'image/svg'], true)
            || strtolower($file->getClientOriginalExtension()) === 'svg';

        // SVG is a vector format — Cloudinary requires resource_type='raw' for it
        // (the default 'image' type is restricted to raster formats by most account plans).
        $options = [
            'resource_type' => $isSvg ? 'raw' : 'image',
        ];

        if (! $isSvg) {
            // Only relevant for raster uploads
            $options['image_metadata'] = false;
        }

        if ($publicId !== null) {
            $options['public_id'] = "davdevs/{$publicId}";
        } else {
            $options['folder'] = 'davdevs';
        }

        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), $options);

        return [
            'cloudinary_id' => $result['public_id'],
            'url'           => $result['secure_url'],
            'width'         => $isSvg ? null : ($result['width'] ?? null),
            'height'        => $isSvg ? null : ($result['height'] ?? null),
            'format'        => $result['format'] ?? ($isSvg ? 'svg' : null),
            'bytes'         => $result['bytes'] ?? null,
        ];
    }

    public function destroy(string $publicId): void
    {
        // Try image type first; if it fails, retry as raw (SVG files are stored as raw)
        try {
            $this->cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
        } catch (\Throwable) {
            $this->cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'raw']);
        }
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
