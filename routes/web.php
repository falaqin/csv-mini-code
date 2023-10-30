<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $files = \App\Models\UploadedFile::orderBy("id", "desc")->get();
    return Inertia::render('CsvDashboard', [
        'uploaded_files' => $files
    ]);
});

require __DIR__.'/auth.php';
