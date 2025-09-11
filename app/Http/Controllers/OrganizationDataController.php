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
        $departments = OrganizationData::getDepartments();

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Get positions list
     */
    public function positions(): JsonResponse
    {
        $positions = OrganizationData::getPositions();

        return response()->json([
            'success' => true,
            'data' => $positions
        ]);
    }

    /**
     * Get skills list
     */
    public function skills(): JsonResponse
    {
        $skills = OrganizationData::getSkills();

        return response()->json([
            'success' => true,
            'data' => $skills
        ]);
    }

    /**
     * Get employment types list
     */
    public function employmentTypes(): JsonResponse
    {
        $types = OrganizationData::getEmploymentTypes();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Get experience levels list
     */
    public function experienceLevels(): JsonResponse
    {
        $levels = OrganizationData::getExperienceLevels();

        return response()->json([
            'success' => true,
            'data' => $levels
        ]);
    }

    /**
     * Get locations list
     */
    public function locations(): JsonResponse
    {
        $locations = OrganizationData::getLocations();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /**
     * Get statuses list
     */
    public function statuses(): JsonResponse
    {
        $statuses = OrganizationData::getStatuses();

        return response()->json([
            'success' => true,
            'data' => $statuses
        ]);
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
