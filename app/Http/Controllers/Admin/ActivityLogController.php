<?php

/**
 * ActivityLogController
 * @author Dmytro Hovenko
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

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
        if ($request->has('user_id') && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->has('action') && $request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by severity
        if ($request->has('severity') && $request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by model type
        if ($request->has('model_type') && $request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->has('search') && $request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->integer('per_page', 15);
        $logs = $query->paginate($perPage);

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
            'users' => $this->getUniqueUsers()
        ];

        return response()->json([
            'success' => true,
            'data' => $options
        ]);
    }

    /**
     * Get unique users from activity logs
     */
    private function getUniqueUsers(): Collection
    {
        return ActivityLog::with('user')
                         ->whereNotNull('user_id')
                         ->get()
                         ->pluck('user')
                         ->filter()
                         ->unique('id')
                         ->values();
    }

    /**
     * Export activity logs
     *
     * @throws ValidationException
     */
    public function export(Request $request): Response|JsonResponse
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
            return $this->exportToCsv($logs, $request->date_from, $request->date_to);
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
     * Export logs to CSV format
     */
    private function exportToCsv(Collection $logs, string $dateFrom, string $dateTo): Response
    {
        $filename = 'activity_logs_' . $dateFrom . '_to_' . $dateTo . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $csv = "ID,User,Action,Description,Model Type,Model ID,Severity,IP Address,User Agent,Created At\n";

        foreach ($logs as $log) {
            $userName = $this->getUserName($log->user);
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $log->id,
                $userName,
                $log->action,
                $this->escapeCsvValue($log->description),
                $log->model_type ?? '',
                $log->model_id ?? '',
                $log->severity,
                $log->ip_address ?? '',
                $this->escapeCsvValue($log->user_agent ?? ''),
                $log->created_at
            );
        }

        return response($csv, 200, $headers);
    }

    /**
     * Get username safely
     */
    private function getUserName(?User $user): string
    {
        if (!$user) {
            return 'System';
        }

        return $user->name ?? $user->email ?? 'System';
    }

    /**
     * Escape CSV values
     */
    private function escapeCsvValue(string $value): string
    {
        return str_replace(',', ';', $value);
    }

    /**
     * Clean old activity logs (for maintenance)
     *
     * @throws ValidationException
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:365' // Keep at least 30 days
        ]);

        $days = $request->integer('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)
                                   ->where('severity', '!=', 'critical') // Keep critical logs longer
                                   ->delete();

        // Log the cleanup action
        ActivityLog::log(
            'cleanup',
            "Cleaned up $deletedCount activity log records older than $days days",
            null,
            null,
            ['deleted_count' => $deletedCount, 'cutoff_date' => $cutoffDate->toDateTimeString()]
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted $deletedCount old activity log records",
            'deleted_count' => $deletedCount
        ]);
    }
}
