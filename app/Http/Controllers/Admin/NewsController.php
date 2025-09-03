<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news = News::orderBy('created_at', 'desc')->paginate(15);
        return response()->json($news);
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
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image_url' => 'nullable|url',
            'author' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $news = News::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'image_url' => $request->image_url,
            'author' => $request->author,
            'category' => $request->category,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $request->is_published ? ($request->published_at ?? now()) : null,
        ]);

        return response()->json($news, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return response()->json($news);
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
    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image_url' => 'nullable|url',
            'author' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $updateData = [
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'image_url' => $request->image_url,
            'author' => $request->author,
            'category' => $request->category,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
        ];

        if ($request->title !== $news->title) {
            $updateData['slug'] = Str::slug($request->title);
        }

        if ($request->boolean('is_published')) {
            $updateData['published_at'] = $request->published_at ?? $news->published_at ?? now();
        }

        $news->update($updateData);

        return response()->json($news);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $news->delete();
        return response()->json(['message' => 'News article deleted successfully']);
    }

    public function categories()
    {
        $categories = News::distinct('category')->pluck('category');
        return response()->json($categories);
    }
}
