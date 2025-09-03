<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teamMembers = TeamMember::orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $teamMembers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'bio' => 'required|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'skills' => 'nullable|string',
            'social_links' => 'nullable|string',
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

        $data = $request->all();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $path = $avatar->storeAs('team/avatars', $filename, 'public');
            $data['avatar'] = '/storage/' . $path;
        }

        // Process skills from JSON or comma-separated string
        if (isset($data['skills'])) {
            if (is_string($data['skills'])) {
                try {
                    // Try to decode as JSON first
                    $skills = json_decode($data['skills'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // If not JSON, treat as comma-separated string
                        $skills = array_map('trim', explode(',', $data['skills']));
                        $skills = array_filter($skills); // Remove empty values
                    }
                    $data['skills'] = $skills;
                } catch (\Exception $e) {
                    $data['skills'] = [];
                }
            }
        }

        // Process social links from JSON string
        if (isset($data['social_links'])) {
            if (is_string($data['social_links'])) {
                try {
                    $data['social_links'] = json_decode($data['social_links'], true);
                } catch (\Exception $e) {
                    $data['social_links'] = [];
                }
            }
        }

        $teamMember = TeamMember::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member created successfully',
            'data' => $teamMember
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamMember $teamMember)
    {
        return response()->json([
            'success' => true,
            'data' => $teamMember
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'bio' => 'required|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'skills' => 'nullable|string',
            'social_links' => 'nullable|string',
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

        $data = $request->all();

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

        // Process skills from JSON or comma-separated string
        if (isset($data['skills'])) {
            if (is_string($data['skills'])) {
                try {
                    // Try to decode as JSON first
                    $skills = json_decode($data['skills'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // If not JSON, treat as comma-separated string
                        $skills = array_map('trim', explode(',', $data['skills']));
                        $skills = array_filter($skills); // Remove empty values
                    }
                    $data['skills'] = $skills;
                } catch (\Exception $e) {
                    $data['skills'] = [];
                }
            }
        }

        // Process social links from JSON string
        if (isset($data['social_links'])) {
            if (is_string($data['social_links'])) {
                try {
                    $data['social_links'] = json_decode($data['social_links'], true);
                } catch (\Exception $e) {
                    $data['social_links'] = [];
                }
            }
        }

        $teamMember->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member updated successfully',
            'data' => $teamMember->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamMember $teamMember)
    {
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

        return response()->json([
            'success' => true,
            'message' => 'Team member deleted successfully'
        ]);
    }
}
