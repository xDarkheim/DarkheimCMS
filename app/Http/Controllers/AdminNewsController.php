<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class AdminNewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = News::query();

        // Фильтрация по статусу
        if ($request->has('status')) {
            switch ($request->status) {
                case 'published':
                    $query->where('is_published', true);
                    break;
                case 'draft':
                    $query->where('is_published', false);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
            }
        }

        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $news = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'image_url' => 'nullable|url',
            'author' => 'required|string|max:255',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $news = News::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'author' => $validated['author'],
            'category' => $validated['category'],
            'is_published' => $validated['is_published'] ?? false,
            'is_featured' => $validated['is_featured'] ?? false,
            'published_at' => $validated['published_at'] ?? null,
        ]);

        // Log news creation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'App\\Models\\News',
            'model_id' => $news->id,
            'description' => "Created news article: {$news->title}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => $news->toArray(),
            'severity' => 'info'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'News article created successfully',
            'data' => $news
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news): JsonResponse
    {
        // Store original data for logging
        $originalData = $news->toArray();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'image_url' => 'nullable|url',
            'author' => 'required|string|max:255',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $news->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'author' => $validated['author'],
            'category' => $validated['category'],
            'is_published' => $validated['is_published'] ?? false,
            'is_featured' => $validated['is_featured'] ?? false,
            'published_at' => $validated['published_at'] ?? null,
        ]);

        // Log news update with changes
        $changes = [];
        $newData = $news->fresh()->toArray();
        foreach ($newData as $key => $newValue) {
            if (isset($originalData[$key]) && $originalData[$key] !== $newValue) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $newValue
                ];
            }
        }

        if (!empty($changes)) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'model_type' => 'App\\Models\\News',
                'model_id' => $news->id,
                'description' => "Updated news article: {$news->title}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => $changes,
                'severity' => 'info'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'News article updated successfully',
            'data' => $news
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news): JsonResponse
    {
        $newsData = $news->toArray();
        $newsTitle = $news->title;
        $newsId = $news->id;

        $news->delete();

        // Log news deletion
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'App\\Models\\News',
            'model_id' => $newsId,
            'description' => "Deleted news article: {$newsTitle}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $newsData,
            'severity' => 'warning'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'News article deleted successfully'
        ]);
    }

    /**
     * Toggle published status
     */
    public function togglePublished(News $news): JsonResponse
    {
        $oldStatus = $news->is_published;
        $news->update(['is_published' => !$news->is_published]);

        // Log status change
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'status_changed',
            'model_type' => 'App\\Models\\News',
            'model_id' => $news->id,
            'description' => "Changed publication status of '{$news->title}' from " . ($oldStatus ? 'published' : 'draft') . " to " . ($news->is_published ? 'published' : 'draft'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => [
                'is_published' => [
                    'old' => $oldStatus,
                    'new' => $news->is_published
                ]
            ],
            'severity' => 'info'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Publication status updated successfully',
            'data' => $news
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(News $news): JsonResponse
    {
        $oldStatus = $news->is_featured;
        $news->update(['is_featured' => !$news->is_featured]);

        // Log featured status change
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'featured_changed',
            'model_type' => 'App\\Models\\News',
            'model_id' => $news->id,
            'description' => "Changed featured status of '{$news->title}' from " . ($oldStatus ? 'featured' : 'normal') . " to " . ($news->is_featured ? 'featured' : 'normal'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => [
                'is_featured' => [
                    'old' => $oldStatus,
                    'new' => $news->is_featured
                ]
            ],
            'severity' => 'info'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Featured status updated successfully',
            'data' => $news
        ]);
    }

    /**
     * Bulk action for multiple news items
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:publish,unpublish,feature,unfeature,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:news,id'
        ]);

        $news = News::whereIn('id', $validated['ids'])->get();
        $affectedTitles = $news->pluck('title')->toArray();

        switch ($validated['action']) {
            case 'publish':
                News::whereIn('id', $validated['ids'])->update(['is_published' => true]);
                break;
            case 'unpublish':
                News::whereIn('id', $validated['ids'])->update(['is_published' => false]);
                break;
            case 'feature':
                News::whereIn('id', $validated['ids'])->update(['is_featured' => true]);
                break;
            case 'unfeature':
                News::whereIn('id', $validated['ids'])->update(['is_featured' => false]);
                break;
            case 'delete':
                News::whereIn('id', $validated['ids'])->delete();
                break;
        }

        // Log bulk action
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'bulk_action',
            'model_type' => 'App\\Models\\News',
            'description' => "Performed bulk action '{$validated['action']}' on " . count($validated['ids']) . " news articles: " . implode(', ', array_slice($affectedTitles, 0, 3)) . (count($affectedTitles) > 3 ? '...' : ''),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => [
                'action' => $validated['action'],
                'affected_ids' => $validated['ids'],
                'affected_titles' => $affectedTitles
            ],
            'severity' => $validated['action'] === 'delete' ? 'warning' : 'info'
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($validated['action']) . ' action completed successfully'
        ]);
    }

    /**
     * Получить список доступных категорий
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => News::getCategories()
        ]);
    }
}
