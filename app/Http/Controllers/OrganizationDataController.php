<?php

namespace App\Http\Controllers;

use App\Models\OrganizationData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrganizationDataController extends Controller
{
    /**
     * Get all organization data by type or all types
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->get('type');

        if ($type) {
            $data = OrganizationData::where('type', $type)
                ->where('is_active', true)
                ->orderBy('order')
                ->orderBy('label')
                ->get();
        } else {
            $data = OrganizationData::where('is_active', true)
                ->orderBy('type')
                ->orderBy('order')
                ->orderBy('label')
                ->get()
                ->groupBy('type');
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get departments list
     */
    public function departments(): JsonResponse
    {
        try {
            $departments = OrganizationData::getDepartments();

            return response()->json([
                'success' => true,
                'data' => $departments ?: []
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load departments: ' . $e->getMessage());

            // Return default departments as fallback
            return response()->json([
                'success' => true,
                'data' => [
                    ['key' => 'engineering', 'label' => 'Engineering'],
                    ['key' => 'design', 'label' => 'Design'],
                    ['key' => 'marketing', 'label' => 'Marketing'],
                    ['key' => 'sales', 'label' => 'Sales']
                ]
            ]);
        }
    }

    /**
     * Get positions list
     */
    public function positions(): JsonResponse
    {
        try {
            $positions = OrganizationData::getPositions();

            return response()->json([
                'success' => true,
                'data' => $positions ?: []
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load positions: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    ['key' => 'developer', 'label' => 'Developer'],
                    ['key' => 'designer', 'label' => 'Designer'],
                    ['key' => 'manager', 'label' => 'Manager']
                ]
            ]);
        }
    }

    /**
     * Get skills list
     */
    public function skills(): JsonResponse
    {
        try {
            $skills = OrganizationData::getSkills();

            return response()->json([
                'success' => true,
                'data' => $skills ?: []
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load skills: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    ['key' => 'javascript', 'label' => 'JavaScript'],
                    ['key' => 'php', 'label' => 'PHP'],
                    ['key' => 'laravel', 'label' => 'Laravel']
                ]
            ]);
        }
    }

    /**
     * Get employment types list
     */
    public function employmentTypes(): JsonResponse
    {
        try {
            $types = OrganizationData::getEmploymentTypes();

            return response()->json([
                'success' => true,
                'data' => $types ?: []
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load employment types: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    ['key' => 'full-time', 'label' => 'Full Time'],
                    ['key' => 'part-time', 'label' => 'Part Time'],
                    ['key' => 'contract', 'label' => 'Contract']
                ]
            ]);
        }
    }

    /**
     * Get experience levels list
     */
    public function experienceLevels(): JsonResponse
    {
        try {
            $levels = OrganizationData::getExperienceLevels();

            return response()->json([
                'success' => true,
                'data' => $levels ?: []
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load experience levels: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    ['key' => 'junior', 'label' => 'Junior'],
                    ['key' => 'mid', 'label' => 'Mid Level'],
                    ['key' => 'senior', 'label' => 'Senior']
                ]
            ]);
        }
    }

    /**
     * Get locations list
     */
    public function locations(): JsonResponse
    {
        try {
            $locations = OrganizationData::getLocations();

            return response()->json([
                'success' => true,
                'data' => $locations ?: []
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load locations: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    ['key' => 'remote', 'label' => 'Remote'],
                    ['key' => 'office', 'label' => 'Office']
                ]
            ]);
        }
    }

    /**
     * Get statuses list
     */
    public function statuses(): JsonResponse
    {
        try {
            $statuses = OrganizationData::getStatuses();

            return response()->json([
                'success' => true,
                'data' => $statuses ?: []
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load statuses: ' . $e->getMessage());

            return response()->json([
                'success' => true,
                'data' => [
                    ['key' => 'active', 'label' => 'Active'],
                    ['key' => 'inactive', 'label' => 'Inactive']
                ]
            ]);
        }
    }

    /**
     * Store a new organization data item
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:organization_data,key',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        $data = OrganizationData::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Organization data created successfully',
            'data' => $data
        ], 201);
    }

    /**
     * Update organization data item
     */
    public function update(Request $request, OrganizationData $organizationData): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:organization_data,key,' . $organizationData->id,
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array'
        ]);

        $organizationData->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Organization data updated successfully',
            'data' => $organizationData
        ]);
    }

    /**
     * Delete organization data item
     */
    public function destroy(OrganizationData $organizationData): JsonResponse
    {
        $organizationData->delete();

        return response()->json([
            'success' => true,
            'message' => 'Organization data deleted successfully'
        ]);
    }

    /**
     * Get all available data types with counts
     */
    public function dataTypes(): JsonResponse
    {
        $types = OrganizationData::selectRaw('type, count(*) as count')
            ->where('is_active', true)
            ->groupBy('type')
            ->orderBy('type')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Bulk update order for organization data items
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:organization_data,id',
            'items.*.order' => 'required|integer|min:0'
        ]);

        foreach ($request->items as $item) {
            OrganizationData::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully'
        ]);
    }
}
