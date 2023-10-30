<?php
namespace App\Enums;

enum FileStatus: string
{
    case PENDING = 'Pending';
    case PROCESSING = 'Processing';
    case COMPLETED = 'Completed';
    case FAILED = 'Failed';
}
