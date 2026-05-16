<?php

namespace App\Jobs;

use App\Enums\ScanStatus;
use App\Models\Scan;
use App\Notifications\ScanCompletedNotification;
use App\Services\OsintService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithQueue;
use Throwable;

#[WithQueue(
    connection: 'redis',
    queue: 'osint',
    tries: 2,
    timeout: 240,
    backoff: 60
)]
class EmailOsintJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Scan $scan) {}

    public function handle(OsintService $osint): void
    {
        $this->scan->update([
            'status'     => ScanStatus::Running,
            'started_at' => now(),
        ]);

        try {
            $report = $osint->run($this->scan);

            $this->scan->update([
                'status'       => ScanStatus::Completed,
                'completed_at' => now(),
            ]);

            // Notify via email if user provided contact email
            if ($this->scan->contact_email) {
                \Illuminate\Support\Facades\Notification::route('mail', $this->scan->contact_email)
                    ->notify(new ScanCompletedNotification($report, $this->scan->contact_email));
            }

        } catch (Throwable $e) {
            $this->scan->update([
                'status'        => ScanStatus::Failed,
                'error_message' => $e->getMessage(),
                'completed_at'  => now(),
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        $this->scan->update([
            'status'        => ScanStatus::Failed,
            'error_message' => 'Le scan a échoué après plusieurs tentatives: ' . $e->getMessage(),
            'completed_at'  => now(),
        ]);
    }
}
