<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $summary = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.logs.index', compact('summary'));
    }

    /**
     * Display the specified activity log.
     */
    public function show(Activity $log)
    {
        $log->load('subject', 'causer');
        return view('admin.logs.show', compact('log'));
    }

    /**
     * Remove the specified activity log from storage.
     */
    public function destroy(Activity $log)
    {
        $log->delete();

        return redirect()->route('admin.logs.index')
            ->with('success', 'Activity log deleted successfully.');
    }

    /**
     * Clear old activity logs
     */
    public function clearOld(Request $request)
    {
        $days = $request->input('days', 30);
        $date = now()->subDays($days);
        
        $deleted = Activity::where('created_at', '<', $date)->delete();

        return redirect()->route('admin.logs.index')
            ->with('success', "Cleared {$deleted} activity logs older than {$days} days.");
    }

    /**
     * Clear all activity logs
     */
    public function clearAll(Request $request)
    {
        $deleted = Activity::count();
        Activity::truncate();

        return redirect()->route('admin.logs.index')
            ->with('success', "Cleared all {$deleted} activity logs.");
    }
} 