<?php

use App\Http\Controllers\ScanController;
use App\Http\Middleware\ThrottleScanRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'))->name('home');

// Scan form page (standalone)
Route::get('/scan/new', [ScanController::class, 'create'])->name('scans.create');

Route::post('/scan', [ScanController::class, 'submit'])
    ->middleware(ThrottleScanRequests::class)
    ->name('scans.submit');

// Both show and status require ?token= query parameter for access control
Route::get('/scan/{uuid}', [ScanController::class, 'show'])->name('scans.show');
Route::get('/scan/{uuid}/status', [ScanController::class, 'status'])->name('scans.status');

Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/about', 'about')->name('about');
