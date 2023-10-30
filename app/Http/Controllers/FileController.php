<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use App\Jobs\ProcessCsvFile;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index()
    {
        $files = UploadedFile::orderBy("id","desc")->get();

        // Use transformer
        return response()->json(['files' => $files]);
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $storagePath = $file->store('public/uploads/csv');
            $fileSize = filesize(storage_path('app/' . $storagePath));
            $fileHash = hash_file('sha256', storage_path('app/' . $storagePath));
            $uniqueId = uniqid();

            // check if there is already a file with exact size and hash has been uploaded
            $existedFile = UploadedFile::where([
                'stored_filesize' => $fileSize,
                'stored_filehash' => $fileHash
            ])->first();

            if ($existedFile) {
                return response()->json(['message' => 'Exact same file has already been uploaded.'], 200);
            }

            // check if similar file has been uploaded with uniqid() for race conditioning
            try {
                $uploadedFile = UploadedFile::create([
                    'unique_id' => $uniqueId,
                    'original_filename' => $file->getClientOriginalName(),
                    'stored_filename' => $storagePath,
                    'stored_filesize' => $fileSize,
                    'stored_filehash' => $fileHash
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle the duplicate key error here
                return response()->json(['message' => 'File with the same unique ID already uploaded.'], 200);
            }

            // Dispatch the queue
            ProcessCsvFile::dispatch($uploadedFile->stored_filename);
            return response()->json(['message' => 'successful']);
        }

        return response()->json([
            'message' => 'unsuccessful'
        ], 400);
    }
}
