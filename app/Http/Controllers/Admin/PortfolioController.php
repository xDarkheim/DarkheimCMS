<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $portfolios = Portfolio::with('portfolioCategory')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return response()->json($portfolios);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Create form data',
            'data' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'image_url' => 'nullable|url',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'url',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'technologies' => 'nullable|array',
            'portfolio_category_id' => 'required|exists:portfolio_categories,id|exists:portfolio_categories,id,is_active,1',
            'client' => 'nullable|string|max:255',
            'completed_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Get the category slug from the selected category
        $category = PortfolioCategory::find($request->portfolio_category_id);

        $portfolio = Portfolio::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'image_url' => $request->image_url,
            'gallery_images' => $request->gallery_images,
            'project_url' => $request->project_url,
            'github_url' => $request->github_url,
            'technologies' => $request->technologies,
            'category' => $category->slug, // Use the category slug from database
            'portfolio_category_id' => $request->portfolio_category_id,
            'client' => $request->client,
            'completed_at' => $request->completed_at,
            'is_featured' => $request->boolean('is_featured', false),
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Log portfolio creation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'App\\Models\\Portfolio',
            'model_id' => $portfolio->id,
            'description' => "Created portfolio: {$portfolio->title}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => $portfolio->toArray(),
            'severity' => 'info'
        ]);

        return response()->json([
            'message' => 'Portfolio created successfully',
            'portfolio' => $portfolio->load('portfolioCategory')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Portfolio $portfolio): JsonResponse
    {
        return response()->json($portfolio->load('portfolioCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Portfolio $portfolio): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $portfolio->load('portfolioCategory')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Portfolio $portfolio): JsonResponse
    {
        // Store original data for logging
        $originalData = $portfolio->toArray();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'image_url' => 'nullable|url',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'url',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'technologies' => 'nullable|array',
            'portfolio_category_id' => 'required|exists:portfolio_categories,id|exists:portfolio_categories,id,is_active,1',
            'client' => 'nullable|string|max:255',
            'completed_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Get the category slug from the selected category
        $category = PortfolioCategory::find($request->portfolio_category_id);

        $portfolio->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'image_url' => $request->image_url,
            'gallery_images' => $request->gallery_images,
            'project_url' => $request->project_url,
            'github_url' => $request->github_url,
            'technologies' => $request->technologies,
            'category' => $category->slug, // Use the category slug from database
            'portfolio_category_id' => $request->portfolio_category_id,
            'client' => $request->client,
            'completed_at' => $request->completed_at,
            'is_featured' => $request->boolean('is_featured', false),
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Log portfolio update with changes
        $changes = [];
        $newData = $portfolio->fresh()?->toArray();
        if ($newData) {
            foreach ($newData as $key => $newValue) {
                if (isset($originalData[$key]) && $originalData[$key] !== $newValue) {
                    $changes[$key] = [
                        'old' => $originalData[$key],
                        'new' => $newValue
                    ];
                }
            }
        }

        if (!empty($changes)) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'model_type' => 'App\\Models\\Portfolio',
                'model_id' => $portfolio->id,
                'description' => "Updated portfolio: {$portfolio->title}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => $changes,
                'severity' => 'info'
            ]);
        }

        return response()->json([
            'message' => 'Portfolio updated successfully',
            'portfolio' => $portfolio->load('portfolioCategory')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portfolio $portfolio): JsonResponse
    {
        $portfolioData = $portfolio->toArray();
        $portfolioTitle = $portfolio->title;
        $portfolioId = $portfolio->id;

        $portfolio->delete();

        // Log portfolio deletion
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'App\\Models\\Portfolio',
            'model_id' => $portfolioId,
            'description' => "Deleted portfolio: {$portfolioTitle}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $portfolioData,
            'severity' => 'warning'
        ]);

        return response()->json([
            'message' => 'Portfolio deleted successfully'
        ]);
    }

    /**
     * Получить все категории портфолио (для админ-панели)
     */
    public function categories(): JsonResponse
    {
        $categories = PortfolioCategory::active()
            ->ordered()
            ->get(['id', 'name', 'slug', 'icon', 'color']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:portfolios,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            Portfolio::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Portfolio items reordered successfully']);
    }
}
