<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $news = News::orderBy('created_at', 'desc')->paginate(15);
        return response()->json($news);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image_url' => 'nullable|url',
            'social_image_url' => 'nullable|url',
            'author' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(News::CATEGORIES)),
            'status' => 'nullable|string|in:' . implode(',', array_keys(News::STATUSES)),
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $news = News::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'image_url' => $request->image_url,
            'social_image_url' => $request->social_image_url,
            'author' => $request->author,
            'category' => $request->category,
            'status' => $request->status ?? News::STATUS_DRAFT,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $request->is_published ? ($request->published_at ?? now()) : null,
            'tags' => $request->tags ? array_map('trim', $request->tags) : null,
            'reading_time' => 0, // Будет вычислено автоматически
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        // Автоматически вычисляем время чтения
        $news->update(['reading_time' => $news->calculateReadingTime()]);

        return response()->json([
            'success' => true,
            'data' => $news,
            'message' => 'News article created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news): JsonResponse
    {
        return response()->json($news);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image_url' => 'nullable|url',
            'social_image_url' => 'nullable|url',
            'author' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(News::CATEGORIES)),
            'status' => 'nullable|string|in:' . implode(',', array_keys(News::STATUSES)),
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $updateData = [
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'image_url' => $request->image_url,
            'social_image_url' => $request->social_image_url,
            'author' => $request->author,
            'category' => $request->category,
            'status' => $request->status ?? News::STATUS_DRAFT,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'tags' => $request->tags ? array_map('trim', $request->tags) : null,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ];

        if ($request->title !== $news->title) {
            $updateData['slug'] = Str::slug($request->title);
        }

        if ($request->boolean('is_published')) {
            $updateData['published_at'] = $request->published_at ?? $news->published_at ?? now();
        }

        // Пересчитываем время чтения при изменении контента
        if ($request->content !== $news->content) {
            $news->content = $request->content;
            $updateData['reading_time'] = $news->calculateReadingTime();
        }

        $news->update($updateData);

        return response()->json([
            'success' => true,
            'data' => $news,
            'message' => 'News article updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news): JsonResponse
    {
        $news->delete();
        return response()->json([
            'success' => true,
            'message' => 'News article deleted successfully'
        ]);
    }

    /**
     * Получить статистику для админки
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => News::count(),
            'published' => News::where('is_published', true)->count(),
            'drafts' => News::where('status', News::STATUS_DRAFT)->count(),
            'scheduled' => News::where('status', News::STATUS_SCHEDULED)->count(),
            'featured' => News::where('is_featured', true)->count(),
            'total_views' => News::sum('views'),
            'categories' => News::select('category', \DB::raw('count(*) as count'))
                              ->groupBy('category')
                              ->get(),
            'recent_activity' => News::latest()->limit(5)->get(['id', 'title', 'status', 'created_at']),
            'top_viewed' => News::orderBy('views', 'desc')->limit(5)->get(['id', 'title', 'views'])
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Массовые операции
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:publish,unpublish,feature,unfeature,archive,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:news,id'
        ]);

        $news = News::whereIn('id', $request->ids);
        $count = $news->count();

        switch ($request->action) {
            case 'publish':
                $news->update(['is_published' => true, 'published_at' => now()]);
                break;
            case 'unpublish':
                $news->update(['is_published' => false]);
                break;
            case 'feature':
                $news->update(['is_featured' => true]);
                break;
            case 'unfeature':
                $news->update(['is_featured' => false]);
                break;
            case 'archive':
                $news->update(['status' => News::STATUS_ARCHIVED]);
                break;
            case 'delete':
                $news->delete();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$count} articles"
        ]);
    }

    /**
     * Получить доступные категории и статусы
     */
    public function metadata(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => News::getCategories(),
                'statuses' => News::getStatuses(),
                'tags' => News::getAllTags()
            ]
        ]);
    }

    /**
     * Фильтрация для админки
     */
    public function filter(Request $request): JsonResponse
    {
        $query = News::query();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['created_at', 'published_at', 'views', 'title'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $news = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }
}
