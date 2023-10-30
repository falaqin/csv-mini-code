<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index()
    {
        $files = UploadedFile::all();

        // Use transformer
        return response()->json(['files' => $files]);
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $storagePath = $file->store('public/uploads/csv');
            $uniqueId = uniqid();

            // check if similar file has been uploaded with uniqid() for race conditioning
            try {
                $uploadedFile = UploadedFile::create([
                    'unique_id' => $uniqueId,
                    'original_filename' => $file->getClientOriginalName(),
                    'stored_filename' => $storagePath
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle the duplicate key error here
                return response()->json(['message' => 'File with the same unique ID already uploaded.'], 200);
            }

            // Dispatch the queue with race condition handling
            \App\Jobs\ProcessCsvFile::dispatch($uploadedFile->stored_filename)->afterResponse();
            return response()->json(['message' => 'successful']);
        }

        return response()->json([
            'message' => 'unsuccessful'
        ], 400);
    }
}
