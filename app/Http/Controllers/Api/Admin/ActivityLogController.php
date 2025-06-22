<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Get activity logs list for admin
     */
    public function index(Request $request)
    {
        $query = Activity::with(['subject', 'causer']);

        // Handle search/filters
        if ($request->has('keyword') && !empty($request->keyword)) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('description', 'like', "%{$keyword}%")
                  ->orWhere('log_name', 'like', "%{$keyword}%")
                  ->orWhere('event', 'like', "%{$keyword}%")
                  ->orWhereHas('causer', function ($causer) use ($keyword) {
                      $causer->where('name', 'like', "%{$keyword}%")
                             ->orWhere('email', 'like', "%{$keyword}%");
                  });
            });
        }

        // Handle status filter
        if ($request->has('status') && $request->status !== 'all') {
            switch ($request->status) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        // Handle log name filter
        if ($request->has('log_name') && $request->log_name !== 'all') {
            $query->where('log_name', $request->log_name);
        }

        // Handle event filter
        if ($request->has('event') && $request->event !== 'all') {
            $query->where('event', $request->event);
        }

        // Handle sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['created_at', 'log_name', 'event', 'description'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Handle pagination
        $perPage = $request->get('per_page', 15);
        if ($perPage == 0) {
            $logs = $query->get();
            $result = [
                'data' => $logs,
                'total' => $logs->count(),
            ];
        } else {
            $result = $query->paginate($perPage);
        }

        // Add computed fields
        if (isset($result['data'])) {
            $data = $result['data'];
        } else {
            $data = $result->items();
        }

        foreach ($data as $log) {
            $log->causer_name = $log->causer ? $log->causer->name : 'System';
            $log->causer_email = $log->causer ? $log->causer->email : null;
            $log->subject_type = $log->subject_type ? class_basename($log->subject_type) : null;
            $log->subject_name = $this->getSubjectName($log);
            $log->formatted_properties = $this->formatProperties($log->properties);
            $log->time_ago = $log->created_at->diffForHumans();
        }

        // Summary for status board
        $summary = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'data' => $data,
            'pagination' => $result instanceof \Illuminate\Pagination\LengthAwarePaginator ? [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ] : null,
            'meta' => [
                'summary' => $summary,
                'log_names' => Activity::distinct()->pluck('log_name')->filter()->values(),
                'events' => Activity::distinct()->pluck('event')->filter()->values(),
            ]
        ]);
    }

    /**
     * Delete activity log
     */
    public function destroy(Activity $log)
    {
        $log->delete();

        $summary = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'message' => 'Activity log deleted successfully.',
            'meta' => [
                'summary' => $summary,
            ]
        ]);
    }

    /**
     * Get subject name based on subject type
     */
    private function getSubjectName($log)
    {
        if (!$log->subject) {
            return null;
        }

        // Try common name fields
        $fields = ['name', 'title', 'email', 'username'];
        
        foreach ($fields as $field) {
            if (isset($log->subject->$field)) {
                return $log->subject->$field;
            }
        }

        return "ID: {$log->subject->id}";
    }

    /**
     * Format properties for display
     */
    private function formatProperties($properties)
    {
        if (!$properties || $properties->isEmpty()) {
            return null;
        }

        $formatted = [];
        
        foreach ($properties as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $formatted[$key] = json_encode($value, JSON_PRETTY_PRINT);
            } else {
                $formatted[$key] = $value;
            }
        }

        return $formatted;
    }
} 