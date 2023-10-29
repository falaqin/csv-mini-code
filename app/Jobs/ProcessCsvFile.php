<?php

namespace App\Jobs;

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

        // TODO after getting the data, use transform to parse the data to required array keys

        // TODO save the content to database
        // When saving the content to database, use lockForUpdate method to handle race

        // TODO save the uploaded file status to completed if nothing goes wrong
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
