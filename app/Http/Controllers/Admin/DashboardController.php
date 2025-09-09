<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Portfolio;
use App\Models\News;
use App\Models\User;
use App\Models\TeamMember;
use App\Models\Career;
use Exception;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get admin dashboard data
     */
    public function index(): JsonResponse
    {
        try {
            $stats = $this->getDashboardStats();
            $recentActivity = $this->getRecentActivity();
            $notifications = $this->getNotifications();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_activity' => $recentActivity,
                    'notifications' => $notifications,
                    'last_updated' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->getDashboardStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activity for dashboard
     */
    public function recentActivity(): JsonResponse
    {
        try {
            $activity = $this->getRecentActivity();

            return response()->json([
                'success' => true,
                'data' => $activity
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load recent activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate dashboard statistics
     * @return array<string, mixed>
     */
    private function getDashboardStats(): array
    {
        $oneMonthAgo = Carbon::now()->subMonth();
        $oneWeekAgo = Carbon::now()->subWeek();

        // Users statistics
        $usersStats = User::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as admin_count,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent_count
            ', ['admin', $oneMonthAgo])
            ->first();

        // Portfolio statistics
        $portfoliosStats = Portfolio::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent
            ', [$oneMonthAgo])
            ->first();

        // News statistics
        $newsStats = News::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent
            ', [$oneMonthAgo])
            ->first();

        // Contact messages statistics
        $contactStats = ContactMessage::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week
            ', [$oneWeekAgo])
            ->first();

        // Team members statistics
        $teamStats = TeamMember::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active,
                COUNT(DISTINCT CASE WHEN department IS NOT NULL THEN department END) as departments
            ', ['active'])
            ->first();

        // Careers statistics
        $careersStats = Career::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
            ')
            ->first();

        // Get the current user for last login
        $currentUser = auth()->user();
        $lastLogin = null;

        if ($currentUser) {
            if ($currentUser->last_login_at) {
                $lastLogin = $currentUser->last_login_at;
            } else {
                $lastLogin = $currentUser->created_at;
            }
        }

        return [
            // Users
            'users_count' => (int) ($usersStats->total ?? 0),
            'admin_users_count' => (int) ($usersStats->admin_count ?? 0),
            'recent_users_count' => (int) ($usersStats->recent_count ?? 0),

            // Portfolios
            'portfolios_count' => (int) ($portfoliosStats->total ?? 0),
            'featured_portfolios_count' => (int) ($portfoliosStats->featured ?? 0),
            'recent_portfolios_count' => (int) ($portfoliosStats->recent ?? 0),

            // News
            'news_count' => (int) ($newsStats->total ?? 0),
            'published_news_count' => (int) ($newsStats->published ?? 0),
            'featured_news_count' => (int) ($newsStats->featured ?? 0),
            'recent_news_count' => (int) ($newsStats->recent ?? 0),

            // Contact Messages
            'contact_messages_count' => (int) ($contactStats->total ?? 0),
            'contact_messages_unread' => (int) ($contactStats->unread ?? 0),
            'contact_messages_today' => (int) ($contactStats->today ?? 0),
            'contact_messages_this_week' => (int) ($contactStats->this_week ?? 0),

            // Team
            'team_members_count' => (int) ($teamStats->total ?? 0),
            'team_members_active' => (int) ($teamStats->active ?? 0),
            'team_departments' => (int) ($teamStats->departments ?? 0),

            // Careers
            'careers_count' => (int) ($careersStats->total ?? 0),
            'careers_active' => (int) ($careersStats->active ?? 0),
            'job_applications' => 0, // Placeholder - implement if needed

            // Security (placeholder - implement with proper logging)
            'failed_logins_24h' => 0,
            'active_sessions' => 1,
            'last_login' => $lastLogin?->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get recent activity for dashboard
     * @return array<int, array<string, mixed>>
     */
    private function getRecentActivity(): array
    {
        // Recent portfolios
        $recentPortfolios = Portfolio::query()
            ->latest()
            ->take(3)
            ->get()
            ->map(function (Portfolio $portfolio): array {
                return [
                    'type' => 'portfolio_created',
                    'title' => 'New portfolio added',
                    'description' => "Portfolio '$portfolio->title' was created",
                    'time' => $portfolio->created_at,
                    'icon' => 'fas fa-briefcase',
                    'color' => 'success'
                ];
            });

        // Recent news
        $recentNews = News::query()
            ->latest()
            ->take(3)
            ->get()
            ->map(function (News $news): array {
                return [
                    'type' => 'news_created',
                    'title' => 'New article published',
                    'description' => "Article '$news->title' was published",
                    'time' => $news->created_at,
                    'icon' => 'fas fa-newspaper',
                    'color' => 'info'
                ];
            });

        // Recent contact messages
        $recentMessages = ContactMessage::query()
            ->latest()
            ->take(3)
            ->get()
            ->map(function (ContactMessage $message): array {
                return [
                    'type' => 'contact_message',
                    'title' => 'New contact message',
                    'description' => "Message from $message->name ($message->email)",
                    'time' => $message->created_at,
                    'icon' => 'fas fa-envelope',
                    'color' => $message->is_read ? 'secondary' : 'warning'
                ];
            });

        // Combine and sort by time
        return collect()
            ->merge($recentPortfolios)
            ->merge($recentNews)
            ->merge($recentMessages)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Get notifications for dashboard
     * @return array<int, array<string, mixed>>
     */
    private function getNotifications(): array
    {
        $notifications = [];

        // Unread messages notification
        $unreadCount = ContactMessage::where('is_read', false)->count();
        if ($unreadCount > 0) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Unread Messages',
                'message' => "You have $unreadCount unread contact message" . ($unreadCount > 1 ? 's' : ''),
                'action' => '/admin/contact-messages',
                'action_text' => 'View Messages'
            ];
        }

        // System notifications (can be extended)
        $diskUsage = $this->getDiskUsagePercentage();
        if ($diskUsage > 80) {
            $notifications[] = [
                'type' => 'danger',
                'title' => 'High Disk Usage',
                'message' => "Disk usage is at $diskUsage%. Consider cleaning up old files.",
                'action' => '/admin/file-manager',
                'action_text' => 'Manage Files'
            ];
        }

        return $notifications;
    }

    /**
     * Get disk usage percentage (simplified implementation)
     */
    private function getDiskUsagePercentage(): int
    {
        // Simplified implementation - in real app, check actual disk usage
        return 45; // Placeholder value
    }
}
