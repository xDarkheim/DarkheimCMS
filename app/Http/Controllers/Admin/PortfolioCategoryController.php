<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PortfolioCategoryController extends Controller
{
    /**
     * Получить список всех категорий
     */
    public function index()
    {
        $categories = PortfolioCategory::withCount(['portfolios' => function ($query) {
                $query->where('is_published', true);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Создать новую категорию
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:portfolio_categories,name',
            'slug' => 'nullable|string|max:255|unique:portfolio_categories,slug',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category = PortfolioCategory::create([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?: 'fas fa-folder',
            'color' => $request->color ?: '#667eea',
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?: 0,
        ]);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Показать конкретную категорию
     */
    public function show(PortfolioCategory $portfolioCategory)
    {
        $portfolioCategory->loadCount(['portfolios' => function ($query) {
            $query->where('is_published', true);
        }]);

        return response()->json([
            'success' => true,
            'data' => $portfolioCategory
        ]);
    }

    /**
     * Обновить категорию
     */
    public function update(Request $request, PortfolioCategory $portfolioCategory)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('portfolio_categories', 'name')->ignore($portfolioCategory->id)
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('portfolio_categories', 'slug')->ignore($portfolioCategory->id)
            ],
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $portfolioCategory->update([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?: 'fas fa-folder',
            'color' => $request->color ?: '#667eea',
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?: 0,
        ]);

        return response()->json([
            'success' => true,
            'data' => $portfolioCategory,
            'message' => 'Category updated successfully'
        ]);
    }

    /**
     * Удалить категорию
     */
    public function destroy(PortfolioCategory $portfolioCategory)
    {
        // Проверяем, есть ли проекты в этой категории
        $projectsCount = $portfolioCategory->portfolios()->count();

        if ($projectsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete category. It contains {$projectsCount} project(s). Please move or delete these projects first."
            ], 422);
        }

        $portfolioCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Получить активные категории для выбора в формах
     */
    public function active()
    {
        $categories = PortfolioCategory::active()
            ->ordered()
            ->get(['id', 'name', 'slug', 'icon', 'color']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Обновить порядок сортировки категорий
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:portfolio_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->categories as $categoryData) {
            PortfolioCategory::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category order updated successfully'
        ]);
    }
}
