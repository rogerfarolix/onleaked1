<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/services', fn () => view('pages.services'))->name('services');
Route::get('/contact', fn () => view('pages.contact'))->name('contact');

// Leak Check & Footprint
Route::get('/leak-check', fn () => view('leak-check'))->name('leak-check');
Route::post('/check-email', [\App\Http\Controllers\LeakCheckController::class, 'check'])->name('check-email');

// Domain Analysis
Route::get('/domain', [\App\Http\Controllers\DomainAnalysisController::class, 'show'])->name('domain.show');
Route::post('/analyze-domain', [\App\Http\Controllers\DomainAnalysisController::class, 'analyze'])->name('domain.analyze');

// PDF Downloads (no auth required — results data is POSTed client-side)
Route::post('/pdf/leak-check', [\App\Http\Controllers\PdfController::class, 'leakCheck'])->name('pdf.leak-check');
Route::post('/pdf/domain', [\App\Http\Controllers\PdfController::class, 'domainAnalysis'])->name('pdf.domain');

// Shareable Domain Results (24h UUID-based pages)
Route::post('/results/store', [\App\Http\Controllers\DomainResultController::class, 'store'])->name('results.store');
Route::get('/results/{uuid}', [\App\Http\Controllers\DomainResultController::class, 'show'])->name('results.domain');

// Authenticated user routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'technologies'     => \App\Models\Technology::all(),
            'userTechnologies' => auth()->user()->technologies->pluck('id')->toArray(),
        ]);
    })->name('dashboard');

    Route::post('/technologies', [\App\Http\Controllers\UserTechnologyController::class, 'update'])->name('technologies.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
});

// Healthcheck
Route::get('/health', function () {
    $checks = [];

    // Database
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $checks['database'] = ['status' => 'ok', 'message' => 'Connected'];
    } catch (\Exception $e) {
        $checks['database'] = ['status' => 'down', 'message' => $e->getMessage()];
    }

    // Cache / Redis
    try {
        $key = 'health-ping-' . time();
        cache()->put($key, true, 5);
        cache()->forget($key);
        $checks['cache'] = ['status' => 'ok', 'message' => 'Responding'];
    } catch (\Exception $e) {
        $checks['cache'] = ['status' => 'down', 'message' => $e->getMessage()];
    }

    // Python service
    $pythonBin  = base_path('python_service/venv/bin/python');
    $scriptPath = base_path('python_service/footprint.py');
    if (file_exists($pythonBin) && file_exists($scriptPath)) {
        $checks['python_service'] = ['status' => 'ok', 'message' => 'Available'];
    } else {
        $checks['python_service'] = ['status' => 'unavailable', 'message' => 'venv or footprint.py not found'];
    }

    $allOk  = collect($checks)->every(fn ($c) => in_array($c['status'], ['ok', 'unavailable']));
    $anyDown = collect($checks)->contains(fn ($c) => $c['status'] === 'down');

    return response()->json([
        'status'    => $anyDown ? 'degraded' : ($allOk ? 'ok' : 'degraded'),
        'checks'    => $checks,
        'timestamp' => now()->toIso8601String(),
    ], $anyDown ? 503 : 200);
})->name('health');

require __DIR__.'/auth.php';
