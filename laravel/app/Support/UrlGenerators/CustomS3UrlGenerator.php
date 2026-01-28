<?php

namespace App\Support\UrlGenerators;

use Spatie\MediaLibrary\Support\UrlGenerator\UrlGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomS3UrlGenerator implements UrlGenerator
{
    private $media;
    private $conversion;
    private $pathGenerator;

    public function setMedia(Media $media): UrlGenerator
    {
        $this->media = $media;
        return $this;
    }

    public function setConversion(Conversion $conversion): UrlGenerator
    {
        $this->conversion = $conversion;
        return $this;
    }

    public function setPathGenerator(PathGenerator $pathGenerator): UrlGenerator
    {
        $this->pathGenerator = $pathGenerator;
        return $this;
    }

    public function getUrl(): string
    {
        // Generate a URL to the Laravel endpoint that serves the file
        return route('media.show', ['filename' => basename($this->getPath())]);
    }

    public function getPath(): string
    {
        $path = $this->media->getPath();

        if ($this->conversion) {
            $path = $this->media->getPath($this->conversion);
        }

        return $path;
    }

    public function getTemporaryUrl(\DateTimeInterface $expiresAt, array $options = []): string
    {
        $disk = $this->media->disk;
        $path = $this->getPath();

        return \Illuminate\Support\Facades\Storage::disk($disk)->temporaryUrl(
            $path,
            $expiresAt,
            $options
        );
    }

    public function getResponsiveImagesUrl(string $conversionName = ''): string
    {
        return $this->getUrl();
    }

    public function getResponsiveImagesDirectoryUrl(): string
    {
        return route('media.show', ['filename' => 'responsive-images']);
    }
}
