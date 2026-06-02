<?php

namespace App\Http\Controllers;

use App\Models\DomainResult;
use Illuminate\Http\Request;

class DomainResultController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'domain'  => ['required', 'string', 'max:253'],
            'results' => ['required', 'array'],
        ]);

        $record = DomainResult::create([
            'domain'     => $request->input('domain'),
            'results'    => $request->input('results'),
            'expires_at' => now()->addHours(24),
        ]);

        return response()->json([
            'url'        => route('results.domain', $record->share_uuid),
            'expires_at' => $record->expires_at->toIso8601String(),
        ]);
    }

    public function show(string $uuid): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $record = DomainResult::where('share_uuid', $uuid)->firstOrFail();

        if ($record->isExpired()) {
            $record->delete();
            return redirect('/')->with('error', 'This shared report has expired.');
        }

        return \Inertia\Inertia::render('Results/Domain', [
            'domain'    => $record->domain,
            'results'   => $record->results,
            'expiresAt' => $record->expires_at->toIso8601String(),
        ]);
    }
}
