<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public pages
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');
Route::get('/services', fn () => Inertia::render('Services'))->name('services');
Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');

// Leak Check & Footprint
Route::get('/leak-check', fn () => Inertia::render('LeakCheck'))->name('leak-check');
Route::post('/check-email', [\App\Http\Controllers\LeakCheckController::class, 'check'])->name('check-email');
Route::get('/footprint-status/{id}', [\App\Http\Controllers\LeakCheckController::class, 'footprintStatus'])->name('footprint.status')->middleware('throttle:30,1');

// Domain Analysis
Route::get('/domain', [\App\Http\Controllers\DomainAnalysisController::class, 'show'])->name('domain.show');
Route::post('/analyze-domain', [\App\Http\Controllers\DomainAnalysisController::class, 'analyze'])->name('domain.analyze');

// Password Breach Check
Route::get('/password-check', [\App\Http\Controllers\PasswordCheckController::class, 'show'])->name('password-check');
Route::post('/check-password', [\App\Http\Controllers\PasswordCheckController::class, 'check'])->name('check-password')->middleware('throttle:5,1');

// SSL Certificate Inspector
Route::get('/ssl-check', [\App\Http\Controllers\SslCheckController::class, 'show'])->name('ssl-check');
Route::post('/check-ssl', [\App\Http\Controllers\SslCheckController::class, 'check'])->name('check-ssl')->middleware('throttle:5,1');

// IP Reputation Check
Route::get('/ip-check', [\App\Http\Controllers\IpCheckController::class, 'show'])->name('ip-check');
Route::post('/check-ip', [\App\Http\Controllers\IpCheckController::class, 'check'])->name('check-ip')->middleware('throttle:5,1');

// Email Header Analyzer
Route::get('/header-check', [\App\Http\Controllers\EmailHeaderController::class, 'show'])->name('header-check');
Route::post('/analyze-header', [\App\Http\Controllers\EmailHeaderController::class, 'analyze'])->name('analyze-header')->middleware('throttle:5,1');

// CSV Downloads
Route::post('/csv/leak-check', [\App\Http\Controllers\CsvController::class, 'leakCheck'])->name('csv.leak-check')->middleware('throttle:10,1');
Route::post('/csv/domain', [\App\Http\Controllers\CsvController::class, 'domainAnalysis'])->name('csv.domain')->middleware('throttle:10,1');

// PDF Downloads (no auth required results data is POSTed client-side)
Route::post('/pdf/leak-check', [\App\Http\Controllers\PdfController::class, 'leakCheck'])->name('pdf.leak-check')->middleware('throttle:10,1');
Route::post('/pdf/domain', [\App\Http\Controllers\PdfController::class, 'domainAnalysis'])->name('pdf.domain')->middleware('throttle:10,1');
Route::post('/pdf/password-check', [\App\Http\Controllers\PdfController::class, 'passwordCheck'])->name('pdf.password-check')->middleware('throttle:10,1');
Route::post('/pdf/ssl-check', [\App\Http\Controllers\PdfController::class, 'sslAnalysis'])->name('pdf.ssl-check')->middleware('throttle:10,1');

// Shareable Domain Results (24h UUID-based pages)
Route::post('/results/store', [\App\Http\Controllers\DomainResultController::class, 'store'])->name('results.store');
Route::get('/results/{uuid}', [\App\Http\Controllers\DomainResultController::class, 'show'])->name('results.domain');

// Authenticated user routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::post('/technologies', [\App\Http\Controllers\UserTechnologyController::class, 'update'])->name('technologies.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/alerts', [ProfileController::class, 'updateAlerts'])->name('profile.alerts');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/history', [\App\Http\Controllers\ScanHistoryController::class, 'index'])->name('history');
    Route::get('/history/{id}', [\App\Http\Controllers\ScanHistoryController::class, 'show'])->name('history.show');

    Route::get('/profile/api-keys', [\App\Http\Controllers\ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/profile/api-keys', [\App\Http\Controllers\ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('/profile/api-keys/{id}', [\App\Http\Controllers\ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/technologies', [\App\Http\Controllers\Admin\AdminTechnologyController::class, 'index'])->name('technologies.index');
    Route::post('/technologies', [\App\Http\Controllers\Admin\AdminTechnologyController::class, 'store'])->name('technologies.store');
    Route::delete('/technologies/{technology}', [\App\Http\Controllers\Admin\AdminTechnologyController::class, 'destroy'])->name('technologies.destroy');
    Route::get('/logs', [\App\Http\Controllers\Admin\AdminLogsController::class, 'index'])->name('logs.index');
    Route::get('/logs/export', [\App\Http\Controllers\Admin\AdminLogsController::class, 'export'])->name('logs.export');
    Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/toggle-role', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleRole'])->name('users.toggle-role');
    Route::post('/users/{id}/suspend', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleSuspend'])->name('users.suspend');
});

// API v1 (API key auth)
Route::prefix('api/v1')->middleware(['api.key', 'throttle:60,1'])->group(function () {
    Route::post('/check-email', [\App\Http\Controllers\LeakCheckController::class, 'check'])->name('api.check-email');
    Route::post('/analyze-domain', [\App\Http\Controllers\DomainAnalysisController::class, 'analyze'])->name('api.domain');
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
