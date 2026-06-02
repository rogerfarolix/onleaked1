<?php

namespace App\Http\Controllers;

use App\Models\Technology;
use App\Models\Vulnerability;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $technologies     = Technology::all();
        $userTechnologies = $user->technologies->pluck('id')->toArray();

        $subscribedTechs = $user->technologies()->with([
            'vulnerabilities' => fn($q) => $q->where('published_at', '>=', now()->subDays(30))->orderBy('published_at', 'desc'),
        ])->get();

        $cveStats = $subscribedTechs->mapWithKeys(function ($tech) {
            $vulns   = $tech->vulnerabilities;
            $counts  = $vulns->groupBy('severity')->map->count();
            return [$tech->id => [
                'total'    => $vulns->count(),
                'critical' => $counts['CRITICAL'] ?? $counts['Critical'] ?? 0,
                'high'     => $counts['HIGH'] ?? $counts['High'] ?? 0,
                'medium'   => $counts['MEDIUM'] ?? $counts['Medium'] ?? 0,
                'low'      => $counts['LOW'] ?? $counts['Low'] ?? 0,
                'recent'   => $vulns->take(5),
            ]];
        });

        return \Inertia\Inertia::render('Dashboard', compact('technologies', 'userTechnologies', 'subscribedTechs', 'cveStats'));
    }
}
