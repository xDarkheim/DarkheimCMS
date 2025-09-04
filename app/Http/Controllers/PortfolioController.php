<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    /**
     * Получить все опубликованные портфолио с фильтрацией и пагинацией
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Portfolio::with('portfolioCategory')
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc');

            // Фильтрация по категории
            if ($request->has('category') && $request->category) {
                if ($request->category !== 'all') {
                    $query->where(function($q) use ($request) {
                        $q->whereHas('portfolioCategory', function($categoryQuery) use ($request) {
                            $categoryQuery->where('slug', $request->category);
                        })->orWhere('category', $request->category);
                    });
                }
            }

            // Поиск по названию
            if ($request->has('search') && $request->search) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $perPage = $request->get('per_page', 10);
            $portfolios = $query->paginate($perPage);

            // Добавляем category_name в каждый элемент
            $portfolios->getCollection()->transform(function ($portfolio) {
                $portfolio->category_name = $portfolio->category_name;
                return $portfolio;
            });

            return response()->json([
                'success' => true,
                'data' => $portfolios->items(),
                'meta' => [
                    'current_page' => $portfolios->currentPage(),
                    'last_page' => $portfolios->lastPage(),
                    'per_page' => $portfolios->perPage(),
                    'total' => $portfolios->total(),
                    'from' => $portfolios->firstItem(),
                    'to' => $portfolios->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch portfolios',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Получить избранные портфолио
     */
    public function featured(): JsonResponse
    {
        try {
            $portfolios = Portfolio::with('portfolioCategory')
                ->where('is_published', true)
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            // Добавляем category_name в каждый элемент
            $portfolios->transform(function ($portfolio) {
                $portfolio->category_name = $portfolio->category_name;
                return $portfolio;
            });

            return response()->json([
                'success' => true,
                'data' => $portfolios
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured portfolios',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Получить доступные категории портфолио
     */
    public function categories(): JsonResponse
    {
        try {
            // Получаем активные категории с количеством проектов
            $categories = PortfolioCategory::active()
                ->ordered()
                ->withCount(['portfolios' => function ($query) {
                    $query->where('is_published', true);
                }])
                ->having('portfolios_count', '>', 0) // Только категории с проектами
                ->get()
                ->mapWithKeys(function ($category) {
                    return [$category->slug => $category->name];
                });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Получить статистику портфолио
     */
    public function stats(): JsonResponse
    {
        try {
            $totalProjects = Portfolio::where('is_published', true)->count();
            $featuredProjects = Portfolio::where('is_published', true)
                ->where('is_featured', true)
                ->count();
            $categoriesCount = Portfolio::where('is_published', true)
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct('category')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_projects' => $totalProjects,
                    'featured_projects' => $featuredProjects,
                    'categories_count' => $categoriesCount,
                    'completion_rate' => 98 // Статическое значение
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch portfolio statistics',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Показать детали проекта по ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $portfolio = Portfolio::where('is_published', true)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $portfolio
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio not found',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }
}
