<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $teamMembers = TeamMember::orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            // Ensure we always return an array, even if empty
            $membersArray = $teamMembers->toArray();

            return response()->json([
                'success' => true,
                'data' => $membersArray,
                'count' => count($membersArray)
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load team members: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Return empty array instead of error to prevent empty pages
            return response()->json([
                'success' => true,
                'data' => [],
                'count' => 0,
                'message' => 'Team members are temporarily unavailable'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Prepare data for validation - convert JSON strings to arrays if needed
        $requestData = $request->all();

        // Handle skills - convert JSON string to array if needed
        if (isset($requestData['skills'])) {
            if (is_string($requestData['skills'])) {
                $skills = json_decode($requestData['skills'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($skills)) {
                    $requestData['skills'] = $skills;
                } else {
                    // If not valid JSON, treat as comma-separated string
                    $requestData['skills'] = array_map('trim', explode(',', $requestData['skills']));
                    $requestData['skills'] = array_filter($requestData['skills']); // Remove empty values
                }
            }
        }

        // Handle social_links - convert JSON string to array if needed
        if (isset($requestData['social_links'])) {
            if (is_string($requestData['social_links'])) {
                $socialLinks = json_decode($requestData['social_links'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($socialLinks)) {
                    $requestData['social_links'] = $socialLinks;
                } else {
                    $requestData['social_links'] = [];
                }
            }
        }

        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'bio' => 'required|string',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'skills' => 'nullable|array',
            'social_links' => 'nullable|array',
            'status' => 'required|in:active,inactive,on-leave',
            'joined_date' => 'nullable|date',
            'priority' => 'nullable|integer|min:0|max:100',
            'show_on_website' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $requestData;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $path = $avatar->storeAs('team/avatars', $filename, 'public');
            $data['avatar'] = '/storage/' . $path;
        }

        // Laravel will automatically handle JSON casting for skills and social_links

        $teamMember = TeamMember::create($data);

        // Log team member creation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'App\\Models\\TeamMember',
            'model_id' => $teamMember->id,
            'description' => "Created team member: {$teamMember->name} ({$teamMember->position})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => $teamMember->toArray(),
            'severity' => 'info'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team member created successfully',
            'data' => $teamMember
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamMember $teamMember): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $teamMember
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamMember $teamMember): JsonResponse
    {
        // Store original data for logging
        $originalData = $teamMember->toArray();

        // Prepare data for validation - convert JSON strings to arrays if needed
        $requestData = $request->all();

        // Handle skills - convert JSON string to array if needed
        if (isset($requestData['skills'])) {
            if (is_string($requestData['skills'])) {
                $skills = json_decode($requestData['skills'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($skills)) {
                    $requestData['skills'] = $skills;
                } else {
                    // If not valid JSON, treat as comma-separated string
                    $requestData['skills'] = array_map('trim', explode(',', $requestData['skills']));
                    $requestData['skills'] = array_filter($requestData['skills']); // Remove empty values
                }
            }
        }

        // Handle social_links - convert JSON string to array if needed
        if (isset($requestData['social_links'])) {
            if (is_string($requestData['social_links'])) {
                $socialLinks = json_decode($requestData['social_links'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($socialLinks)) {
                    $requestData['social_links'] = $socialLinks;
                } else {
                    $requestData['social_links'] = [];
                }
            }
        }

        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'bio' => 'required|string',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'skills' => 'nullable|array',
            'social_links' => 'nullable|array',
            'status' => 'required|in:active,inactive,on-leave',
            'joined_date' => 'nullable|date',
            'priority' => 'nullable|integer|min:0|max:100',
            'show_on_website' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $requestData;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($teamMember->avatar && file_exists(public_path($teamMember->avatar))) {
                unlink(public_path($teamMember->avatar));
            }

            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $path = $avatar->storeAs('team/avatars', $filename, 'public');
            $data['avatar'] = '/storage/' . $path;
        }

        // Laravel will automatically handle JSON casting for skills and social_links

        $teamMember->update($data);

        // Log team member update with changes
        $changes = [];
        $newData = $teamMember->fresh()->toArray();
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
                'model_type' => 'App\\Models\\TeamMember',
                'model_id' => $teamMember->id,
                'description' => "Updated team member: {$teamMember->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => $changes,
                'severity' => 'info'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Team member updated successfully',
            'data' => $teamMember->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamMember $teamMember): JsonResponse
    {
        $memberData = $teamMember->toArray();
        $memberName = $teamMember->name;
        $memberId = $teamMember->id;

        // Delete avatar file if exists
        if ($teamMember->avatar && !empty($teamMember->avatar)) {
            $avatarPath = public_path($teamMember->avatar);
            if (file_exists($avatarPath)) {
                try {
                    unlink($avatarPath);
                } catch (\Exception $e) {
                    // Log the error but don't fail the deletion
                    \Log::warning('Failed to delete avatar file: ' . $e->getMessage(), [
                        'team_member_id' => $teamMember->id,
                        'avatar_path' => $avatarPath
                    ]);
                }
            }
        }

        $teamMember->delete();

        // Log team member deletion
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'App\\Models\\TeamMember',
            'model_id' => $memberId,
            'description' => "Deleted team member: {$memberName}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $memberData,
            'severity' => 'warning'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team member deleted successfully'
        ]);
    }

    /**
     * Toggle visibility of team member on website
     */
    public function toggleVisible(Request $request, TeamMember $teamMember): JsonResponse
    {
        $originalValue = $teamMember->show_on_website;
        $teamMember->show_on_website = !$teamMember->show_on_website;
        $teamMember->save();

        // Log the visibility toggle
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => 'App\\Models\\TeamMember',
            'model_id' => $teamMember->id,
            'description' => "Toggled visibility for team member: {$teamMember->name} (" .
                           ($teamMember->show_on_website ? 'visible' : 'hidden') . " on website)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => [
                'show_on_website' => [
                    'old' => $originalValue,
                    'new' => $teamMember->show_on_website
                ]
            ],
            'severity' => 'info'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Team member visibility toggled successfully',
            'data' => $teamMember
        ]);
    }
}
