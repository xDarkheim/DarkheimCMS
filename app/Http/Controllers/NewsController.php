<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::where('is_published', true)
                    ->orderBy('published_at', 'desc');

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('featured') && $request->featured === 'true') {
            $query->where('is_featured', true);
        }

        $news = $query->paginate(6);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function show($slug)
    {
        $article = News::where('slug', $slug)
                      ->where('is_published', true)
                      ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        // Increment views
        $article->increment('views');

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }

    public function featured()
    {
        $news = News::where('is_published', true)
                   ->where('is_featured', true)
                   ->orderBy('published_at', 'desc')
                   ->limit(3)
                   ->get();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function latest()
    {
        $news = News::where('is_published', true)
                   ->orderBy('published_at', 'desc')
                   ->limit(6)
                   ->get();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function categories()
    {
        // Возвращаем предопределенные категории с их отображаемыми названиями
        $categories = News::getCategories();

        // Также можем вернуть только те категории, которые используются в опубликованных новостях
        $usedCategories = News::where('is_published', true)
                             ->distinct()
                             ->pluck('category')
                             ->filter()
                             ->mapWithKeys(function ($category) use ($categories) {
                                 return [$category => $categories[$category] ?? $category];
                             });

        return response()->json([
            'success' => true,
            'data' => $usedCategories
        ]);
    }

    /**
     * Получить все доступные категории (для фильтрации)
     */
    public function allCategories()
    {
        return response()->json([
            'success' => true,
            'data' => News::getCategories()
        ]);
    }
}
