<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\TeamMember;
use App\Models\Career;
use App\Models\News;
use App\Models\ContactMessage;
use App\Models\User;

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
                'team_members' => TeamMember::where('status', 'active')
                    ->where('show_on_website', true)
                    ->count(),
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
        $totalUsers = User::count();
        $portfolios = Portfolio::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured
        ')->first();

        $news = News::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published
        ')->first();

        $teamMembers = TeamMember::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN show_on_website = 1 THEN 1 ELSE 0 END) as visible
        ')->first();

        $careers = Career::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
        ')->first();

        $contactMessages = ContactMessage::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "unread" THEN 1 ELSE 0 END) as unread
        ')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'users_count' => $totalUsers,
                'portfolios_count' => $portfolios->total ?? 0,
                'featured_portfolios_count' => $portfolios->featured ?? 0,
                'news_count' => $news->total ?? 0,
                'published_news_count' => $news->published ?? 0,
                'team_members_count' => $teamMembers->total ?? 0,
                'team_members_active' => $teamMembers->active ?? 0,
                'team_members_visible' => $teamMembers->visible ?? 0,
                'careers_count' => $careers->total ?? 0,
                'careers_active' => $careers->active ?? 0,
                'contact_messages_count' => $contactMessages->total ?? 0,
                'contact_messages_unread' => $contactMessages->unread ?? 0,
            ]
        ]);
    }
}
