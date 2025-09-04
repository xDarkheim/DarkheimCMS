<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends Controller
{
    /**
     * Get activity logs with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by severity
        if ($request->has('severity') && $request->severity) {
            $query->where('severity', $request->severity);
        }

        // Filter by model type
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Get activity log statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_activities' => ActivityLog::count(),
            'today_activities' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week_activities' => ActivityLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month_activities' => ActivityLog::whereMonth('created_at', now()->month)
                                                 ->whereYear('created_at', now()->year)
                                                 ->count(),

            // Security alerts (critical severity)
            'security_alerts' => ActivityLog::where('severity', 'critical')
                                           ->whereDate('created_at', '>=', now()->subDays(30))
                                           ->count(),

            // Failed login attempts in last 24 hours
            'failed_logins_24h' => ActivityLog::where('action', 'login_failed')
                                             ->whereDate('created_at', '>=', now()->subDay())
                                             ->count(),

            // Activity by severity
            'by_severity' => ActivityLog::selectRaw('severity, COUNT(*) as count')
                                       ->groupBy('severity')
                                       ->pluck('count', 'severity')
                                       ->toArray(),

            // Activity by action
            'by_action' => ActivityLog::selectRaw('action, COUNT(*) as count')
                                     ->groupBy('action')
                                     ->orderBy('count', 'desc')
                                     ->limit(10)
                                     ->pluck('count', 'action')
                                     ->toArray(),

            // Recent critical activities
            'recent_critical' => ActivityLog::where('severity', 'critical')
                                           ->with('user')
                                           ->orderBy('created_at', 'desc')
                                           ->limit(5)
                                           ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get available filter options
     */
    public function filterOptions(): JsonResponse
    {
        $options = [
            'actions' => ActivityLog::distinct()->pluck('action')->filter()->values(),
            'severities' => ['info', 'warning', 'critical', 'success'],
            'model_types' => ActivityLog::distinct()->pluck('model_type')->filter()->values(),
            'users' => ActivityLog::with('user')
                                 ->whereNotNull('user_id')
                                 ->get()
                                 ->pluck('user')
                                 ->unique('id')
                                 ->values()
        ];

        return response()->json([
            'success' => true,
            'data' => $options
        ]);
    }

    /**
     * Export activity logs (for compliance/auditing)
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,json',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        $logs = ActivityLog::with('user')
            ->whereBetween('created_at', [$request->date_from, $request->date_to])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->format === 'csv') {
            $filename = 'activity_logs_' . $request->date_from . '_to_' . $request->date_to . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ];

            $csv = "ID,User,Action,Description,Model Type,Model ID,Severity,IP Address,User Agent,Created At\n";

            foreach ($logs as $log) {
                $csv .= sprintf(
                    "%d,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                    $log->id,
                    $log->user ? $log->user->email : 'System',
                    $log->action,
                    str_replace(',', ';', $log->description),
                    $log->model_type ?: '',
                    $log->model_id ?: '',
                    $log->severity,
                    $log->ip_address ?: '',
                    str_replace(',', ';', $log->user_agent ?: ''),
                    $log->created_at
                );
            }

            return response($csv, 200, $headers);
        }

        // JSON export
        return response()->json([
            'success' => true,
            'data' => $logs,
            'exported_at' => now(),
            'total_records' => $logs->count()
        ]);
    }

    /**
     * Clean old activity logs (for maintenance)
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365' // Keep at least 30 days
        ]);

        $cutoffDate = now()->subDays($request->days);

        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)
                                   ->where('severity', '!=', 'critical') // Keep critical logs longer
                                   ->delete();

        // Log the cleanup action
        ActivityLog::log(
            'cleanup',
            "Cleaned up {$deletedCount} activity log records older than {$request->days} days",
            null,
            null,
            ['deleted_count' => $deletedCount, 'cutoff_date' => $cutoffDate],
            'info'
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} old activity log records",
            'deleted_count' => $deletedCount
        ]);
    }
}
