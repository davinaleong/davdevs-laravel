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

        $baseOptions = [];

        if ($publicId !== null) {
            $baseOptions['public_id'] = "davdevs/{$publicId}";
        } else {
            $baseOptions['folder'] = 'davdevs';
        }

        // Try uploading as 'image'. If Cloudinary rejects it (e.g. SVG blocked on this plan,
        // or the file is a vector format), fall back to resource_type='raw' automatically.
        try {
            $options = array_merge($baseOptions, [
                'resource_type' => 'image',
                'image_metadata' => false,
            ]);
            $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), $options);
            $isSvg = false; // successfully stored as image
        } catch (\Cloudinary\Api\Exception\NotAllowed|\Cloudinary\Api\Exception\BadRequest $e) {
            // Retry as raw (covers SVG and other non-raster formats)
            $options = array_merge($baseOptions, ['resource_type' => 'raw']);
            $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), $options);
            $isSvg = true; // mark so we skip width/height
        }

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
