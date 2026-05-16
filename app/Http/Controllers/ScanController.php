<?php

namespace App\Http\Controllers;

use App\Enums\ScanStatus;
use App\Http\Requests\ScanEmailRequest;
use App\Jobs\EmailOsintJob;
use App\Models\Scan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function create(): View
    {
        return view('scans.create');
    }

    public function submit(ScanEmailRequest $request): RedirectResponse
    {
        // Check for existing recent completed scan for same email (rate limit friendly)
        $existing = Scan::where('email_target', $request->email)
            ->whereIn('status', [ScanStatus::Completed, ScanStatus::Cached])
            ->where('completed_at', '>', now()->subHours(6))
            ->latest()
            ->first();

        if ($existing) {
            return redirect()->route('scans.show', [
                'uuid'  => $existing->uuid,
                'token' => $existing->access_token,
            ]);
        }

        $scan = Scan::create([
            'email_target'  => $request->email,
            'contact_email' => $request->notify_email ?? null,
            'status'        => ScanStatus::Pending,
            'ip_address'    => $request->ip(),
        ]);

        EmailOsintJob::dispatch($scan);

        return redirect()->route('scans.show', [
            'uuid'  => $scan->uuid,
            'token' => $scan->access_token,
        ]);
    }

    public function show(string $uuid, Request $request): View
    {
        $scan = Scan::where('uuid', $uuid)
            ->with(['report', 'results'])
            ->firstOrFail();

        // Validate access token — deny if missing or wrong
        if (! $scan->validateToken($request->query('token'))) {
            abort(403, 'Accès refusé. Ce lien est invalide ou a expiré.');
        }

        return view('scans.show', compact('scan'));
    }

    public function status(string $uuid, Request $request): JsonResponse
    {
        $scan = Scan::where('uuid', $uuid)
            ->with('report')
            ->firstOrFail();

        // Validate access token
        if (! $scan->validateToken($request->query('token'))) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        return response()->json([
            'status'         => $scan->status->value,
            'completed'      => $scan->isCompleted(),
            'failed'         => $scan->isFailed(),
            'accounts_found' => $scan->report?->accounts_found,
            'breaches_found' => $scan->report?->breaches_found,
            'risk_level'     => $scan->report?->risk_level?->value,
            'duration'       => $scan->duration(),
        ]);
    }
}
