<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminNewsController extends Controller
{
    public function index(Request $request)
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image_url' => 'nullable|url',
            'author' => 'required|string|max:100',
            'category' => ['required', 'string', Rule::in(array_keys(News::CATEGORIES))],
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Генерируем slug
        $validated['slug'] = Str::slug($validated['title']);

        // Проверяем уникальность slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (News::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Устанавливаем дату публикации если не указана
        if ($validated['is_published'] && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $news = News::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'News article created successfully',
            'data' => $news
        ], 201);
    }

    public function show(News $news)
    {
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image_url' => 'nullable|url',
            'author' => 'required|string|max:100',
            'category' => ['required', 'string', Rule::in(array_keys(News::CATEGORIES))],
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Обновляем slug если изменился title
        if ($validated['title'] !== $news->title) {
            $baseSlug = Str::slug($validated['title']);
            $validated['slug'] = $baseSlug;

            // Проверяем уникальность slug
            $counter = 1;
            while (News::where('slug', $validated['slug'])->where('id', '!=', $news->id)->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        // Устанавливаем дату публикации при первой публикации
        if ($validated['is_published'] && !$news->is_published && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'News article updated successfully',
            'data' => $news->fresh()
        ]);
    }

    public function destroy(News $news)
    {
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'News article deleted successfully'
        ]);
    }

    public function togglePublished(News $news)
    {
        $news->update([
            'is_published' => !$news->is_published,
            'published_at' => !$news->is_published ? now() : $news->published_at
        ]);

        return response()->json([
            'success' => true,
            'message' => 'News article status updated',
            'data' => $news->fresh()
        ]);
    }

    public function toggleFeatured(News $news)
    {
        $news->update([
            'is_featured' => !$news->is_featured
        ]);

        return response()->json([
            'success' => true,
            'message' => 'News article featured status updated',
            'data' => $news->fresh()
        ]);
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:news,id',
            'action' => 'required|in:publish,unpublish,feature,unfeature,delete'
        ]);

        $news = News::whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'publish':
                $news->update([
                    'is_published' => true,
                    'published_at' => now()
                ]);
                $message = 'Articles published successfully';
                break;
            case 'unpublish':
                $news->update(['is_published' => false]);
                $message = 'Articles unpublished successfully';
                break;
            case 'feature':
                $news->update(['is_featured' => true]);
                $message = 'Articles featured successfully';
                break;
            case 'unfeature':
                $news->update(['is_featured' => false]);
                $message = 'Articles unfeatured successfully';
                break;
            case 'delete':
                $news->delete();
                $message = 'Articles deleted successfully';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Получить список доступных категорий
     */
    public function categories()
    {
        return response()->json([
            'success' => true,
            'data' => News::getCategories()
        ]);
    }
}
