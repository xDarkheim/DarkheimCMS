<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Get all settings grouped by category
     */
    public function index(): JsonResponse
    {
        $settings = Setting::all()->groupBy('group');

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): JsonResponse
    {
        $settings = Setting::where('group', $group)->get()->keyBy('key');

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update multiple settings
     */
    public function updateGroup(Request $request, string $group): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.type' => 'string|in:string,boolean,integer,json,array',
            'settings.*.description' => 'nullable|string',
            'settings.*.is_public' => 'boolean'
        ]);

        foreach ($request->settings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                [
                    'value' => $settingData['value'],
                    'type' => $settingData['type'] ?? 'string',
                    'group' => $group,
                    'description' => $settingData['description'] ?? null,
                    'is_public' => $settingData['is_public'] ?? false
                ]
            );
        }

        Setting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Update a single setting
     */
    public function update(Request $request, string $key): JsonResponse
    {
        $request->validate([
            'value' => 'required',
            'type' => 'string|in:string,boolean,integer,json,array',
            'group' => 'string',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            $request->only(['value', 'type', 'group', 'description', 'is_public'])
        );

        Setting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting
        ]);
    }

    /**
     * Delete a setting
     */
    public function destroy(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $setting->delete();
        Setting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully'
        ]);
    }

    /**
     * Get public settings (for frontend)
     */
    public function getPublic(): JsonResponse
    {
        $settings = Setting::getPublic();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Reset settings to default values
     */
    public function resetToDefaults(): JsonResponse
    {
        // Define default settings
        $defaults = [
            // General settings
            'site_name' => ['value' => 'Darkheim Development Studio', 'group' => 'general', 'type' => 'string', 'is_public' => true],
            'site_description' => ['value' => 'Professional web development and design services', 'group' => 'general', 'type' => 'string', 'is_public' => true],
            'admin_email' => ['value' => 'admin@darkheim.dev', 'group' => 'general', 'type' => 'string', 'is_public' => false],
            'items_per_page' => ['value' => 15, 'group' => 'general', 'type' => 'integer', 'is_public' => false],

            // Security settings
            'session_timeout' => ['value' => 60, 'group' => 'security', 'type' => 'integer', 'is_public' => false],
            'max_login_attempts' => ['value' => 5, 'group' => 'security', 'type' => 'integer', 'is_public' => false],
            'require_email_verification' => ['value' => true, 'group' => 'security', 'type' => 'boolean', 'is_public' => false],

            // Email settings
            'contact_form_emails' => ['value' => ['admin@darkheim.dev'], 'group' => 'email', 'type' => 'array', 'is_public' => false],
            'smtp_enabled' => ['value' => false, 'group' => 'email', 'type' => 'boolean', 'is_public' => false],

            // API settings
            'api_rate_limit' => ['value' => 100, 'group' => 'api', 'type' => 'integer', 'is_public' => false],
            'api_cache_ttl' => ['value' => 3600, 'group' => 'api', 'type' => 'integer', 'is_public' => false],
        ];

        foreach ($defaults as $key => $config) {
            Setting::updateOrCreate(
                ['key' => $key],
                $config
            );
        }

        Setting::clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Settings reset to defaults successfully'
        ]);
    }
}
