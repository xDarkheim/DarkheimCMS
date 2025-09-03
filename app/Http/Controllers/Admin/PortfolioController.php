<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $portfolios = Portfolio::orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($portfolios);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            'category' => 'required|string|max:100',
            'client' => 'nullable|string|max:255',
            'completed_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $portfolio = Portfolio::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'short_description' => $request->short_description ?: '',
            'image_url' => $request->image_url,
            'gallery_images' => $request->gallery_images ?? [],
            'project_url' => $request->project_url,
            'github_url' => $request->github_url,
            'technologies' => $request->technologies ?? [],
            'category' => $request->category,
            'client' => $request->client,
            'completed_at' => $request->completed_at ?: now(),
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json($portfolio, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Portfolio $portfolio)
    {
        return response()->json($portfolio);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Portfolio $portfolio)
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
            'category' => 'required|string|max:100',
            'client' => 'nullable|string|max:255',
            'completed_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'short_description' => $request->short_description ?: '',
            'image_url' => $request->image_url,
            'gallery_images' => $request->gallery_images ?? [],
            'project_url' => $request->project_url,
            'github_url' => $request->github_url,
            'technologies' => $request->technologies ?? [],
            'category' => $request->category,
            'client' => $request->client,
            'completed_at' => $request->completed_at,
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published'),
            'sort_order' => $request->sort_order ?? 0,
        ];

        if ($request->title !== $portfolio->title) {
            $updateData['slug'] = Str::slug($request->title);
        }

        $portfolio->update($updateData);

        return response()->json($portfolio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portfolio $portfolio)
    {
        try {
            // Добавляем детальное логирование для отладки
            \Log::info('Attempting to delete portfolio', [
                'portfolio_id' => $portfolio->id,
                'portfolio_title' => $portfolio->title,
                'portfolio_category' => $portfolio->category,
                'user_id' => auth()->id(),
            ]);

            // Проверяем, существует ли портфолио
            if (!$portfolio->exists) {
                \Log::warning('Portfolio does not exist', ['portfolio_id' => $portfolio->id]);
                return response()->json([
                    'message' => 'Portfolio not found',
                    'error' => 'The requested portfolio does not exist'
                ], 404);
            }

            // Сохраняем информацию для логирования
            $portfolioData = [
                'id' => $portfolio->id,
                'title' => $portfolio->title,
                'category' => $portfolio->category
            ];

            // Выполняем удаление
            $deleted = $portfolio->delete();

            if ($deleted) {
                \Log::info('Portfolio deleted successfully', $portfolioData);
                return response()->json([
                    'message' => 'Portfolio item deleted successfully',
                    'deleted_id' => $portfolioData['id'],
                    'deleted_title' => $portfolioData['title']
                ]);
            } else {
                \Log::error('Portfolio deletion failed - delete() returned false', $portfolioData);
                return response()->json([
                    'message' => 'Failed to delete portfolio item',
                    'error' => 'Database deletion failed'
                ], 500);
            }

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error during portfolio deletion', [
                'portfolio_id' => $portfolio->id ?? 'unknown',
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'sql_state' => $e->errorInfo[0] ?? 'unknown'
            ]);

            return response()->json([
                'message' => 'Database error occurred while deleting the portfolio',
                'error' => 'A database constraint or connection issue prevented deletion'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Unexpected error during portfolio deletion', [
                'portfolio_id' => $portfolio->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred while deleting the portfolio',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function categories()
    {
        $categories = Portfolio::distinct('category')->pluck('category');
        return response()->json($categories);
    }

    public function reorder(Request $request)
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
