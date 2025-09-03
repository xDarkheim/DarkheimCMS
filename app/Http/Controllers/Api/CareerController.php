<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CareerController extends Controller
{
    public function index(): JsonResponse
    {
        $careers = Career::active()
            ->byPriority()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $careers
        ]);
    }

    public function show(Career $career): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $career
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'location' => 'required|string|max:255',
            'remote_available' => 'boolean',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'experience_level' => 'required|in:junior,mid,senior,lead',
            'skills' => 'nullable|array',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'application_deadline' => 'nullable|date',
        ]);

        $career = Career::create($validated);

        return response()->json([
            'success' => true,
            'data' => $career,
            'message' => 'Career position created successfully'
        ], 201);
    }

    public function update(Request $request, Career $career): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'location' => 'required|string|max:255',
            'remote_available' => 'boolean',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'experience_level' => 'required|in:junior,mid,senior,lead',
            'skills' => 'nullable|array',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'application_deadline' => 'nullable|date',
        ]);

        $career->update($validated);

        return response()->json([
            'success' => true,
            'data' => $career,
            'message' => 'Career position updated successfully'
        ]);
    }

    public function destroy(Career $career): JsonResponse
    {
        $career->delete();

        return response()->json([
            'success' => true,
            'message' => 'Career position deleted successfully'
        ]);
    }
}
