<?php

namespace App\Http\Controllers;

use App\Models\ScanHistory;
use Illuminate\Http\Request;

class ScanHistoryController extends Controller
{
    public function index()
    {
        $history = ScanHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return \Inertia\Inertia::render('History/Index', compact('history'));
    }

    public function show(int $id)
    {
        $record = ScanHistory::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json($record);
    }
}
