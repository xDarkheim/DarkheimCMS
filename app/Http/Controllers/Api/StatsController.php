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

class StatsController extends Controller
{
    /**
     * Get general statistics for the homepage
     */
    public function public()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'projects_completed' => Portfolio::where('is_featured', true)->count(),
                'total_projects' => Portfolio::count(),
                'team_members' => TeamMember::count(),
                'years_experience' => 1, // Исправлено: компания работает 1 год
                'open_positions' => Career::where('is_active', true)->count(),
                'news_articles' => News::where('is_published', true)->count(),
            ]
        ]);
    }

    /**
     * Get detailed statistics for admin dashboard
     */
    public function admin()
    {
        // Users statistics
        $usersStats = User::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN role = "admin" THEN 1 ELSE 0 END) as admin_count,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent_count
        ', [Carbon::now()->subMonth()])->first();

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
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent
        ', [Carbon::now()->subMonth()])->first();

        // Contact messages statistics
        $contactStats = ContactMessage::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "unread" THEN 1 ELSE 0 END) as unread,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week
        ', [Carbon::now()->subWeek()])->first();

        // Team members statistics (count all team members for admin)
        $teamStats = TeamMember::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
                COUNT(DISTINCT department) as departments
            ')->first();

        // Careers statistics
        $careersStats = Career::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
        ')->first();

        // Job applications count (placeholder)
        $jobApplications = 0;

        // Security statistics
        $failedLogins24h = 0;
        $activeSessions = 1; // Current user session

        // Get last login for current user
        $lastLogin = auth()->user()->last_login_at ?? auth()->user()->created_at;

        return response()->json([
            'success' => true,
            'data' => [
                // Users
                'users_count' => $usersStats->total ?? 0,
                'admin_users_count' => $usersStats->admin_count ?? 0,
                'recent_users_count' => $usersStats->recent_count ?? 0,

                // Portfolios
                'portfolios_count' => $portfoliosStats->total ?? 0,
                'featured_portfolios_count' => $portfoliosStats->featured ?? 0,
                'recent_portfolios_count' => $portfoliosStats->recent ?? 0,

                // News
                'news_count' => $newsStats->total ?? 0,
                'published_news_count' => $newsStats->published ?? 0,
                'recent_news_count' => $newsStats->recent ?? 0,

                // Contact Messages
                'contact_messages_count' => $contactStats->total ?? 0,
                'contact_messages_unread' => $contactStats->unread ?? 0,
                'contact_messages_today' => $contactStats->today ?? 0,
                'contact_messages_this_week' => $contactStats->this_week ?? 0,

                // Team (visible only)
                'team_members_count' => $teamStats->total ?? 0,
                'team_members_active' => $teamStats->active ?? 0,
                'team_departments' => $teamStats->departments ?? 0,

                // Careers
                'careers_count' => $careersStats->total ?? 0,
                'careers_active' => $careersStats->active ?? 0,
                'job_applications' => $jobApplications,

                // Security
                'failed_logins_24h' => $failedLogins24h,
                'active_sessions' => $activeSessions,
                'last_login' => $lastLogin,
            ]
        ]);
    }
}
