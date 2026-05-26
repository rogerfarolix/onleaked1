<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/check-email', [\App\Http\Controllers\LeakCheckController::class, 'check'])->name('check-email');

Route::get('/domain', [\App\Http\Controllers\DomainAnalysisController::class, 'show'])->name('domain.show');
Route::post('/analyze-domain', [\App\Http\Controllers\DomainAnalysisController::class, 'analyze'])->name('domain.analyze');

Route::get('/services', function () { return view('pages.services'); })->name('services');
Route::get('/contact', function () { return view('pages.contact'); })->name('contact');

Route::get('/dashboard', function () {
    return view('dashboard', [
        'technologies' => \App\Models\Technology::all(),
        'userTechnologies' => auth()->user()->technologies->pluck('id')->toArray(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        return view('admin.dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::post('/technologies', [\App\Http\Controllers\UserTechnologyController::class, 'update'])->name('technologies.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
