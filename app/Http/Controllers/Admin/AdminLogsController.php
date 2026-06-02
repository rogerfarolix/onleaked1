<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class AdminLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) => $q->where('email', 'like', '%' . $request->input('user') . '%'));
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->input('action') . '%');
        }
        if ($request->filled('ip')) {
            $query->where('ip_address', $request->input('ip'));
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->input('date_to') . ' 23:59:59');
        }

        $logs = $query->paginate(25)->withQueryString();

        return \Inertia\Inertia::render('Admin/Logs', compact('logs'));
    }

    public function export(Request $request)
    {
        $query = AdminLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) => $q->where('email', 'like', '%' . $request->input('user') . '%'));
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->input('action') . '%');
        }
        if ($request->filled('ip')) {
            $query->where('ip_address', $request->input('ip'));
        }

        $logs   = $query->limit(5000)->get();
        $output = fopen('php://temp', 'rw');
        fputcsv($output, ['ID', 'User', 'Action', 'IP Address', 'User Agent', 'Date']);

        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                $log->user?->email ?? 'N/A',
                $log->action,
                $log->ip_address,
                $log->user_agent,
                $log->created_at?->toDateTimeString(),
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="admin-logs-' . now()->format('Ymd') . '.csv"',
        ]);
    }
}
