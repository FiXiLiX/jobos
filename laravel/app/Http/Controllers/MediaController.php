<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    /**
     * Retrieve and stream a media file from S3
     */
    public function show(string $filename): Response
    {
        // User must be authenticated
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        // Find the media by filename
        $media = Media::where('file_name', $filename)->firstOr(function () {
            abort(404, 'File not found');
        });

        $disk = Storage::disk($media->disk);
        $path = $media->getPath();

        // Check if file exists on S3
        if (!$disk->exists($path)) {
            abort(404, 'File not found on storage');
        }

        // Get file content and MIME type
        $fileContent = $disk->get($path);
        $mimeType = $media->mime_type;

        return response($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
        ]);
    }
}
