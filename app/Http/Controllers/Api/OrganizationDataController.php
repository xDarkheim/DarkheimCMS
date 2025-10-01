<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganizationData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationDataController extends Controller
{
    /**
     * Get all organization data grouped by type
     */
    public function index(): JsonResponse
    {
        $data = [
            'departments' => OrganizationData::getDepartments(),
            'positions' => OrganizationData::getPositions(),
            'skills' => OrganizationData::getSkills(),
            'employment_types' => OrganizationData::getEmploymentTypes(),
            'experience_levels' => OrganizationData::getExperienceLevels(),
            'locations' => OrganizationData::getLocations(),
            'statuses' => OrganizationData::getStatuses(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get data by specific type
     */
    public function getByType(Request $request, string $type): JsonResponse
    {
        $validTypes = ['department', 'position', 'skill', 'employment_type', 'experience_level', 'location', 'status'];

        if (!in_array($type, $validTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type. Valid types: ' . implode(', ', $validTypes)
            ], 400);
        }

        $data = OrganizationData::getByType($type);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get departments for dropdown
     */
    public function departments(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrganizationData::departments()->get()
        ]);
    }

    /**
     * Get positions for dropdown
     */
    public function positions(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrganizationData::positions()->get()
        ]);
    }

    /**
     * Get skills for dropdown
     */
    public function skills(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrganizationData::skills()->get()
        ]);
    }

    /**
     * Get employment types for dropdown
     */
    public function employmentTypes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrganizationData::employmentTypes()->get()
        ]);
    }

    /**
     * Get experience levels for dropdown
     */
    public function experienceLevels(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrganizationData::experienceLevels()->get()
        ]);
    }

    /**
     * Get locations for dropdown
     */
    public function locations(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrganizationData::locations()->get()
        ]);
    }

    /**
     * Get statuses for dropdown
     */
    public function statuses(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrganizationData::statuses()->get()
        ]);
    }

    /**
     * Search across all organization data
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $type = $request->get('type', null);

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $builder = OrganizationData::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('label', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('key', 'LIKE', "%{$query}%");
            });

        if ($type) {
            $builder->where('type', $type);
        }

        $results = $builder->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'query' => $query,
            'count' => $results->count()
        ]);
    }

    /**
     * Get statistics about organization data
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'departments' => OrganizationData::where('type', 'department')->where('is_active', true)->count(),
            'positions' => OrganizationData::where('type', 'position')->where('is_active', true)->count(),
            'skills' => OrganizationData::where('type', 'skill')->where('is_active', true)->count(),
            'employment_types' => OrganizationData::where('type', 'employment_type')->where('is_active', true)->count(),
            'experience_levels' => OrganizationData::where('type', 'experience_level')->where('is_active', true)->count(),
            'locations' => OrganizationData::where('type', 'location')->where('is_active', true)->count(),
            'statuses' => OrganizationData::where('type', 'status')->where('is_active', true)->count(),
            'total' => OrganizationData::where('is_active', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
