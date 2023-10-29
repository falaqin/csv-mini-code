<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    use HasFactory;

    protected $table = 'files_uploaded';

    public $primaryKey = 'id';

    protected $fillable = [
        'unique_id',
        'original_filename',
        'stored_filename',
    ];
}
