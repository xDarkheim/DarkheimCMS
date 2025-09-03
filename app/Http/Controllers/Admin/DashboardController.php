<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\News;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        $stats = [
            'users_count' => User::count(),
            'portfolios_count' => Portfolio::count(),
            'news_count' => News::count(),
            'featured_portfolios_count' => Portfolio::where('is_featured', true)->count(),
            'published_news_count' => News::where('is_published', true)->count(),
            'admin_users_count' => User::where('role', 'admin')->count(),
            'recent_users_count' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'recent_portfolios_count' => Portfolio::where('created_at', '>=', now()->subDays(30))->count(),
            'recent_news_count' => News::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json($stats);
    }
}
