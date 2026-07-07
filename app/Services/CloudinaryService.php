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
        $options = [
            'resource_type' => 'image',
            // Cloudinary strips EXIF/metadata from the delivered asset by default;
            // this explicitly keeps only color-profile data needed for correct rendering.
            'image_metadata' => false,
        ];

        if ($publicId !== null) {
            $options['public_id'] = "davdevs/{$publicId}";
        } else {
            $options['folder'] = 'davdevs';
        }

        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), $options);

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
