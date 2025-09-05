<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\TeamMember;
use App\Models\Career;
use App\Models\News;
use App\Models\ContactMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    /**
     * Get general statistics for the homepage
     */
    public function public(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'projects_completed' => Portfolio::where('is_published', true)->count(),
                'total_projects' => Portfolio::count(),
                'featured_projects' => Portfolio::where('is_featured', true)->count(),
                'team_members' => TeamMember::count(),
                'years_experience' => 1, // Исправлено: компания работает 1 год
                'open_positions' => Career::where('is_active', true)->count(),
                'news_articles' => News::where('is_published', true)->count(),
                'client_satisfaction' => 98, // Добавляем показатель удовлетворенности клиентов
                'technologies_used' => 12, // Количество используемых технологий
            ]
        ]);
    }

    /**
     * Get detailed statistics for admin dashboard
     */
    public function admin(): JsonResponse
    {
        // Users statistics
        $usersStats = User::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as admin_count,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent_count
        ', ['admin', Carbon::now()->subMonth()])->first();

        // Portfolio statistics
        $portfoliosStats = Portfolio::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent
        ', [Carbon::now()->subMonth()])->first();

        // News statistics
        $newsStats = News::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published,
            SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent
        ', [Carbon::now()->subMonth()])->first();

        // Contact messages statistics
        $contactStats = ContactMessage::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week
        ', [Carbon::now()->subWeek()])->first();

        // Team members statistics
        $teamStats = TeamMember::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active,
            COUNT(DISTINCT CASE WHEN department IS NOT NULL THEN department END) as departments
        ', ['active'])->first();

        // Careers statistics
        $careersStats = Career::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
        ')->first();

        // Job applications count (placeholder - should be implemented if applications table exists)
        $jobApplications = 0;

        // Security statistics (placeholder - should be implemented with proper logging)
        $failedLogins24h = 0;
        $activeSessions = 1; // Current user session

        // Get last login for current user
        $currentUser = auth()->user();
        $lastLogin = $currentUser->last_login_at ?? $currentUser->created_at;

        return response()->json([
            'success' => true,
            'data' => [
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
                'job_applications' => (int) $jobApplications,

                // Security
                'failed_logins_24h' => (int) $failedLogins24h,
                'active_sessions' => (int) $activeSessions,
                'last_login' => $lastLogin ? $lastLogin->format('Y-m-d H:i:s') : null,

                // Additional metadata
                'last_updated' => now()->format('Y-m-d H:i:s'),
                'server_time' => now()->format('c'), // ISO 8601 format
            ]
        ]);
    }
}
