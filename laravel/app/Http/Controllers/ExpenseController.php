<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    /**
     * Retrieve and stream a bill picture from S3
     */
    public function billPicture(Expense $expense): StreamedResponse
    {
        // Verify the authenticated user has access to this expense
        $this->authorize('view', $expense);

        $media = $expense->getFirstMedia('bill_pictures');

        if (!$media) {
            abort(404, 'Bill picture not found');
        }

        // Get file from S3
        $fileContent = Storage::disk('s3')->get($media->getPath());

        return response()->streamDownload(
            fn() => print($fileContent),
            $media->file_name,
            [
                'Content-Type' => $media->mime_type,
                'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
            ]
        );
    }
}
