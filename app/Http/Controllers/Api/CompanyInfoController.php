<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use Illuminate\Http\JsonResponse;

class CompanyInfoController extends Controller
{
    /**
     * Get public company info for frontend
     */
    public function index(): JsonResponse
    {
        $contactInfo = CompanyInfo::getContactInfo()->map(function ($info) {
            return [
                'key' => $info->key,
                'label' => $info->label,
                'value' => $info->value,
                'type' => $info->type,
                'icon' => $info->icon,
                'formatted_value' => $info->formatted_value,
                'metadata' => $info->metadata
            ];
        });

        $responseTime = CompanyInfo::getByKey('response_time');

        return response()->json([
            'success' => true,
            'data' => [
                'contacts' => $contactInfo,
                'response_time_text' => $responseTime ? $responseTime->value : null
            ]
        ]);
    }
}
