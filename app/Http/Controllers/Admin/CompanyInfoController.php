<?php

/**
 * CompanyInfoController
 * @author Dmytro Hovenko
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CompanyInfoController extends Controller
{
    /**
     * Display a listing of company info
     */
    public function index(): JsonResponse
    {
        $companyInfos = CompanyInfo::orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $companyInfos
        ]);
    }

    /**
     * Store a newly created company info
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string|unique:company_infos,key|max:255',
                'label' => 'required|string|max:255',
                'value' => 'required|string',
                'type' => 'required|string|in:text,email,phone,address,url,textarea,social',
                'icon' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0',
                'metadata' => 'nullable|array'
            ]);

            $companyInfo = CompanyInfo::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Company info created successfully',
                'data' => $companyInfo
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified company info
     */
    public function show(CompanyInfo $companyInfo): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $companyInfo
        ]);
    }

    /**
     * Update the specified company info
     */
    public function update(Request $request, CompanyInfo $companyInfo): JsonResponse
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string|unique:company_infos,key,' . $companyInfo->id . '|max:255',
                'label' => 'required|string|max:255',
                'value' => 'required|string',
                'type' => 'required|string|in:text,email,phone,address,url,textarea,social',
                'icon' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0',
                'metadata' => 'nullable|array'
            ]);

            $companyInfo->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Company info updated successfully',
                'data' => $companyInfo->fresh()
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified company info
     */
    public function destroy(CompanyInfo $companyInfo): JsonResponse
    {
        $companyInfo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company info deleted successfully'
        ]);
    }

    /**
     * Update sort order of company infos
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:company_infos,id',
            'items.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['items'] as $item) {
            CompanyInfo::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully'
        ]);
    }

    /**
     * Get public company info for frontend
     */
    public function public(): JsonResponse
    {
        $contactInfo = CompanyInfo::getContactInfo();
        $socialLinks = CompanyInfo::getSocialLinks();

        return response()->json([
            'success' => true,
            'data' => [
                'contact_info' => $contactInfo,
                'social_links' => $socialLinks
            ]
        ]);
    }
}
