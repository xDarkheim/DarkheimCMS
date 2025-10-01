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
        $contactInfo = CompanyInfo::getContactInfo();

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
