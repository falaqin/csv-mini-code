<?php

namespace App\Jobs;

use App\Models\Product;
use App\Enums\FileStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCsvFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file;

    /**
     * Create a new job instance.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileContents = storage_path('app/' . $this->file);
        $data = $this->parse($fileContents);

        // Update the status to processing
        $uploadedFile = \App\Models\UploadedFile::where('stored_filename', $this->file)
            ->lockForUpdate()
            ->first();

        // Check if the file is in processing/completed state. If so, don't execute.
        if (
            $uploadedFile->status != FileStatus::PROCESSING->value ||
            $uploadedFile->status != FileStatus::COMPLETED->value
        )
        {
            $uploadedFile->status = FileStatus::PROCESSING->value;
            $uploadedFile->save();
        } else {
            $this->job->delete();
        }

        try {
            $totalData = count($data);
            for ($i = 0; $i < $totalData; $i++) {
                \DB::transaction(function () use ($data, &$i) {
                    $content = $data[$i];
                    $toBeFilled = [
                        'id' => $content['UNIQUE_KEY'],
                        'title' => $content['PRODUCT_TITLE'],
                        'description' => $content['PRODUCT_DESCRIPTION'],
                        'style' => $content['STYLE#'],
                        'sanmar_mainframe_color' => $content['SANMAR_MAINFRAME_COLOR'],
                        'size' => $content['SIZE'],
                        'color_name' => $content['COLOR_NAME'],
                        'piece_price' => $content['PIECE_PRICE'],
                    ];

                    $product = Product::lockForUpdate()->find($content['UNIQUE_KEY']);
                    if (!$product) {
                        $product = Product::create($toBeFilled);
                    } else {
                        $product->fill($toBeFilled);

                        if ($product->isDirty()) {
                            $product->save();
                        }
                    }
                });

                // We save the percentage of completion into uploadedFile model
                $percentage = round((($i + 1) / $totalData) * 100, 2);
                $uploadedFile->completion_percentage = $percentage;
                if ($uploadedFile->isDirty()) {
                    $uploadedFile->save();
                }
            }

            // Save the uploaded file status to completed if nothing goes wrong
            $uploadedFile->status = FileStatus::COMPLETED->value;
            $uploadedFile->save();
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            // Don't change the completion percentage, it might be useful for debugging purposes.
            $uploadedFile->status = FileStatus::FAILED->value;
            $uploadedFile->save();
        }

        $this->job->delete();
    }

    private function parse($csv, $delimiter = ',')
    {
        $data = [];
        $header = null;
        if (($handle = fopen($csv, 'r')) !== false) {
            while (($row = fgetcsv($handle, null, $delimiter)) !== false) {
                // Clean the row for non-UTF-8 characters
                $row = $this->cleanUp($row);

                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    private function cleanUp($row)
    {
        // Function to remove non-UTF-8 characters
        $removeNonUTF8 = function ($str) {
            return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        };

        $encodedRow = array_map($removeNonUTF8, $row);

        // Remove non-UTF-8 characters using regular expression
        $cleanedRow = array_map(function ($cell) {
            // Remove any characters that are not valid UTF-8
            return preg_replace('/[^\p{L}\p{N}\p{P}\p{Z}]/u', '', $cell);
        }, $encodedRow);

        return $cleanedRow;
    }
}
