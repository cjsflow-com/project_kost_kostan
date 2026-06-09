<?php

use App\Http\Controllers\ReportDownloadController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/reports/{report}/download', [ReportDownloadController::class, 'download'])
    ->name('reports.download');
