<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "product";

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $fillable = [
        "id",
        "title",
        "description",
        "style",
        "sanmar_mainframe_color",
        "size",
        "color_name",
        "piece_price",
    ];

    public $date = [
        "created_at",
        "updated_at"
    ];
}
