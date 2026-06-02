<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\ScanHistory;
use App\Models\Technology;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'    => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'tech_count'     => Technology::count(),
            'scans_today'    => ScanHistory::whereDate('created_at', today())->count(),
        ];

        $recentLogs = AdminLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get();

        return \Inertia\Inertia::render('Admin/Dashboard', compact('stats', 'recentLogs'));
    }
}
