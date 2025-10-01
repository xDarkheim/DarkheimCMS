<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    /**
     * Get paginated list of published news
     */
    public function index(Request $request): JsonResponse
    {
        $query = News::published()->orderBy('published_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Tag filter
        if ($request->filled('tag')) {
            $query->byTag($request->tag);
        }

        // Featured filter
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Recent filter (last 30 days)
        if ($request->boolean('recent')) {
            $query->recent(30);
        }

        // Popular filter
        if ($request->boolean('popular')) {
            $query->popular();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['published_at', 'views', 'title', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 12), 50);
        $news = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news,
            'meta' => [
                'categories' => News::getCategories(),
                'tags' => News::getAllTags(),
                'stats' => News::getCategoryStats()
            ]
        ]);
    }

    /**
     * Get single news article by slug
     */
    public function show(string $slug): JsonResponse
    {
        $article = News::where('slug', $slug)
                      ->published()
                      ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        // Increment views
        $article->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }

    /**
     * Get related articles for a specific news item
     */
    public function related(string $slug): JsonResponse
    {
        $article = News::where('slug', $slug)
                      ->published()
                      ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $relatedArticles = $article->getRelatedArticles(3);

        return response()->json([
            'success' => true,
            'data' => $relatedArticles
        ]);
    }

    /**
     * Get featured news articles
     */
    public function featured(): JsonResponse
    {
        $news = News::published()
                   ->featured()
                   ->orderBy('published_at', 'desc')
                   ->limit(6)
                   ->get();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Get latest news articles
     */
    public function latest(): JsonResponse
    {
        $news = News::published()
                   ->orderBy('published_at', 'desc')
                   ->limit(8)
                   ->get();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Get all available categories
     */
    public function allCategories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => News::getCategories()
        ]);
    }

    /**
     * Get category statistics
     */
    public function categoryStats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => News::getCategoryStats()
        ]);
    }

    /**
     * Get all available tags
     */
    public function tags(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => News::getAllTags()
        ]);
    }

    /**
     * Get global news statistics
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => News::getGlobalStats()
        ]);
    }

    /**
     * Get news by category
     */
    public function byCategory(string $category): JsonResponse
    {
        if (!News::isValidCategory($category)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category'
            ], 400);
        }

        $news = News::published()
                   ->byCategory($category)
                   ->orderBy('published_at', 'desc')
                   ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $news,
            'meta' => [
                'category' => $category,
                'category_name' => News::getCategories()[$category] ?? $category
            ]
        ]);
    }

    /**
     * Search news articles
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'category' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        $query = News::published()->search($request->q);

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        $perPage = $request->get('per_page', 12);
        $results = $query->orderBy('published_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $results,
            'meta' => [
                'query' => $request->q,
                'total_results' => $results->total()
            ]
        ]);
    }

    /**
     * Get news archive (grouped by month/year)
     */
    public function archive(): JsonResponse
    {
        $archive = News::published()
                      ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as count')
                      ->groupBy('year', 'month')
                      ->orderBy('year', 'desc')
                      ->orderBy('month', 'desc')
                      ->get()
                      ->map(function ($item): array {
                          $year = (int) $item->getAttribute('year');
                          $month = (int) $item->getAttribute('month');
                          $count = (int) $item->getAttribute('count');

                          return [
                              'year' => $year,
                              'month' => $month,
                              'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                              'count' => $count,
                              'url' => "/news/archive/{$year}/{$month}"
                          ];
                      });

        return response()->json([
            'success' => true,
            'data' => $archive
        ]);
    }

    /**
     * Get news by archive date
     */
    public function archiveDate(int $year, int $month): JsonResponse
    {
        if ($month < 1 || $month > 12) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid month'
            ], 400);
        }

        $news = News::published()
                   ->whereYear('published_at', $year)
                   ->whereMonth('published_at', $month)
                   ->orderBy('published_at', 'desc')
                   ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $news,
            'meta' => [
                'year' => $year,
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'archive_url' => "/news/archive/{$year}/{$month}"
            ]
        ]);
    }

    /**
     * Get sitemap data for news
     */
    public function sitemap(): JsonResponse
    {
        $news = News::published()
                   ->select(['slug', 'title', 'published_at', 'updated_at'])
                   ->orderBy('published_at', 'desc')
                   ->get()
                   ->map(function ($article) {
                       return [
                           'url' => "/news/{$article->slug}",
                           'title' => $article->title,
                           'published_at' => $article->published_at->toISOString(),
                           'updated_at' => $article->updated_at->toISOString()
                       ];
                   });

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }
}
