<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CareerController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $careers = Career::active()
                ->byPriority()
                ->latest()
                ->get();

            // Ensure we always return an array, even if empty
            $careersArray = $careers->toArray();

            return response()->json([
                'success' => true,
                'data' => $careersArray,
                'count' => count($careersArray)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load careers: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Return empty array instead of error to prevent empty pages
            return response()->json([
                'success' => true,
                'data' => [],
                'count' => 0,
                'message' => 'Careers are temporarily unavailable'
            ]);
        }
    }

    public function show(Career $career): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $career
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to show career: ' . $e->getMessage(), [
                'career_id' => $career->id ?? 'unknown',
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Career not found'
            ], 404);
        }
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

        // Log career creation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'App\\Models\\Career',
            'model_id' => $career->id,
            'description' => "Created career position: {$career->title} in {$career->department}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => $career->toArray(),
            'severity' => 'info'
        ]);

        return response()->json([
            'success' => true,
            'data' => $career,
            'message' => 'Career position created successfully'
        ], 201);
    }

    public function update(Request $request, Career $career): JsonResponse
    {
        // Store original data for logging
        $originalData = $career->toArray();

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

        // Log career update with changes
        $changes = [];
        $newData = $career->fresh()->toArray();
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
                'model_type' => 'App\\Models\\Career',
                'model_id' => $career->id,
                'description' => "Updated career position: {$career->title}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => $changes,
                'severity' => 'info'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $career,
            'message' => 'Career position updated successfully'
        ]);
    }

    public function destroy(Career $career): JsonResponse
    {
        $careerData = $career->toArray();
        $careerTitle = $career->title;
        $careerId = $career->id;

        $career->delete();

        // Log career deletion
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'App\\Models\\Career',
            'model_id' => $careerId,
            'description' => "Deleted career position: {$careerTitle}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $careerData,
            'severity' => 'warning'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Career position deleted successfully'
        ]);
    }
}
