<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
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
            $query = Portfolio::where('is_published', true)
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc');

            // Фильтрация по категории
            if ($request->has('category') && $request->category) {
                $query->where('category', $request->category);
            }

            // Поиск по названию
            if ($request->has('search') && $request->search) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $perPage = $request->get('per_page', 10);
            $portfolios = $query->paginate($perPage);

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
            $portfolios = Portfolio::where('is_published', true)
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

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
            // Получаем все уникальные категории из опубликованных проектов
            $categories = Portfolio::where('is_published', true)
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->filter()
                ->mapWithKeys(function ($category) {
                    // Создаем slug из названия категории для ID
                    $slug = strtolower(str_replace([' ', '_'], '-', $category));
                    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
                    return [$slug => $category];
                });

            return response()->json([
                'success' => true,
                'data' => $categories->toArray()
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
    public function show($id): JsonResponse
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
