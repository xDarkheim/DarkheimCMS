<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Portfolio::published()->ordered();

        // Фильтрация по категории
        if ($request->has('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // Только избранные проекты
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Поиск
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhereJsonContains('technologies', $search);
            });
        }

        // Пагинация
        $portfolios = $query->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $portfolios->items(),
            'meta' => [
                'current_page' => $portfolios->currentPage(),
                'last_page' => $portfolios->lastPage(),
                'per_page' => $portfolios->perPage(),
                'total' => $portfolios->total(),
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Portfolio $portfolio): JsonResponse
    {
        if (!$portfolio->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Portfolio not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $portfolio->load(''),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:portfolios,slug',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'image_url' => 'nullable|url',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'url',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'technologies' => 'required|array',
            'technologies.*' => 'string',
            'category' => 'required|string|in:web,mobile,desktop,design',
            'client' => 'nullable|string|max:255',
            'completed_at' => 'required|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $portfolio = Portfolio::create($validated);

        return response()->json([
            'success' => true,
            'data' => $portfolio,
            'message' => 'Portfolio created successfully'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Portfolio $portfolio): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', Rule::unique('portfolios', 'slug')->ignore($portfolio->id)],
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'image_url' => 'nullable|url',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'url',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'technologies' => 'required|array',
            'technologies.*' => 'string',
            'category' => 'required|string|in:web,mobile,desktop,design',
            'client' => 'nullable|string|max:255',
            'completed_at' => 'required|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $portfolio->update($validated);

        return response()->json([
            'success' => true,
            'data' => $portfolio,
            'message' => 'Portfolio updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portfolio $portfolio): JsonResponse
    {
        $portfolio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Portfolio deleted successfully'
        ]);
    }

    /**
     * Get portfolio categories
     */
    public function categories(): JsonResponse
    {
        $categories = [
            'web' => 'Web Development',
            'mobile' => 'Mobile Apps',
            'desktop' => 'Desktop Applications',
            'design' => 'UI/UX Design',
        ];

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get featured portfolios
     */
    public function featured(): JsonResponse
    {
        $portfolios = Portfolio::published()->featured()->ordered()->take(6)->get();

        return response()->json([
            'success' => true,
            'data' => $portfolios
        ]);
    }
}
