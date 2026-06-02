<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class DigitalFootprintJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 90;
    public int $tries   = 1;

    public function __construct(
        private readonly string $email,
        private readonly string $jobId
    ) {}

    public function handle(): void
    {
        $scriptPath = base_path('python_service/footprint.py');
        $pythonBin  = base_path('python_service/venv/bin/python');

        if (!file_exists($scriptPath) || !file_exists($pythonBin)) {
            Cache::put('footprint:' . $this->jobId, ['status' => 'done', 'data' => []], now()->addMinutes(10));
            return;
        }

        $process = new Process([$pythonBin, $scriptPath, $this->email]);
        $process->setTimeout(80);

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                Log::debug('footprint.py stderr: ' . $process->getErrorOutput());
                Cache::put('footprint:' . $this->jobId, ['status' => 'error'], now()->addMinutes(5));
                return;
            }

            $decoded = json_decode($process->getOutput(), true);

            if (!is_array($decoded)) {
                Cache::put('footprint:' . $this->jobId, ['status' => 'done', 'data' => []], now()->addMinutes(10));
                return;
            }

            $sanitized = array_values(array_filter(array_map(function ($site) {
                $clean = preg_replace('/[^a-zA-Z0-9._\-]/', '', (string) $site);
                return (strlen($clean) >= 2 && strlen($clean) <= 100) ? $clean : null;
            }, array_slice($decoded, 0, 200))));

            Cache::put('footprint:' . $this->jobId, ['status' => 'done', 'data' => $sanitized], now()->addMinutes(10));

        } catch (ProcessTimedOutException) {
            Cache::put('footprint:' . $this->jobId, ['status' => 'error'], now()->addMinutes(5));
        } catch (\Exception $e) {
            Log::error('DigitalFootprintJob failed: ' . $e->getMessage());
            Cache::put('footprint:' . $this->jobId, ['status' => 'error'], now()->addMinutes(5));
        }
    }
}
