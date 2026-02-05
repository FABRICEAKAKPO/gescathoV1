<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        // Vérifier que c'est un admin
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('activity-logs.index', compact('logs'));
    }

    public function show($id): View
    {
        // Vérifier que c'est un admin
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $log = ActivityLog::with('user')->findOrFail($id);

        return view('activity-logs.show', compact('log'));
    }
}
