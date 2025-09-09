<?php

/**
 * ContactMessageController
 * @author Dmytro Hovenko
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContactMessageController extends Controller
{
    /**
     * Get all contact messages with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = ContactMessage::query()->orderBy('created_at', 'desc');

        // Filter by read status
        if ($request->has('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Filter by message type
        if ($request->has('type') && $request->type) {
            $query->where('message_type', $request->type);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('company', 'like', $searchTerm)
                  ->orWhere('message', 'like', $searchTerm);
            });
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $messages = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Get contact messages statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('is_read', false)->count(),
            'today' => ContactMessage::whereDate('created_at', today())->count(),
            'this_week' => ContactMessage::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => ContactMessage::whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)
                                         ->count(),

            // Job applications
            'job_applications' => ContactMessage::where('message_type', 'job_application')->count(),
            'job_applications_unread' => ContactMessage::where('message_type', 'job_application')
                                                       ->where('is_read', false)
                                                       ->count(),

            // General inquiries
            'general_inquiries' => ContactMessage::where('message_type', '!=', 'job_application')
                                                 ->orWhereNull('message_type')
                                                 ->count(),

            // Recent activity (last 7 days)
            'recent_messages' => ContactMessage::where('created_at', '>=', now()->subDays(7))->count(),

            // By service type
            'by_service' => ContactMessage::whereNotNull('service')
                                          ->selectRaw('service, COUNT(*) as count')
                                          ->groupBy('service')
                                          ->pluck('count', 'service')
                                          ->toArray()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get single contact message
     */
    public function show(ContactMessage $contactMessage): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $contactMessage
        ]);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(ContactMessage $contactMessage): JsonResponse
    {
        if ($contactMessage->is_read) {
            return response()->json([
                'success' => true,
                'message' => 'Message is already marked as read',
                'data' => $contactMessage
            ]);
        }

        $contactMessage->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read successfully',
            'data' => $contactMessage
        ]);
    }

    /**
     * Delete contact message
     */
    public function destroy(ContactMessage $contactMessage): JsonResponse
    {
        // Delete associated resume file if exists
        if ($contactMessage->resume_file) {
            Storage::disk('public')->delete($contactMessage->resume_file);
        }

        $contactMessage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact message deleted successfully'
        ]);
    }

    /**
     * Download resume file
     */
    public function downloadResume(ContactMessage $contactMessage): JsonResponse|BinaryFileResponse
    {
        if (!$contactMessage->resume_file) {
            return response()->json([
                'success' => false,
                'message' => 'No resume file found for this message'
            ], 404);
        }

        $filePath = storage_path('app/public/' . $contactMessage->resume_file);

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Resume file not found on server'
            ], 404);
        }

        $filename = 'resume_' . $contactMessage->name . '_' . $contactMessage->id . '.' . pathinfo($contactMessage->resume_file, PATHINFO_EXTENSION);

        return response()->download($filePath, $filename);
    }

    /**
     * Mark multiple messages as read
     */
    public function bulkMarkAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contact_messages,id'
        ]);

        $updatedCount = ContactMessage::whereIn('id', $request->ids)
                                      ->where('is_read', false)
                                      ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => "Marked $updatedCount messages as read",
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Delete multiple messages
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contact_messages,id'
        ]);

        $messages = ContactMessage::whereIn('id', $request->ids)->get();

        // Delete associated resume files
        foreach ($messages as $message) {
            if ($message->resume_file) {
                Storage::disk('public')->delete($message->resume_file);
            }
        }

        $deletedCount = ContactMessage::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted $deletedCount messages successfully",
            'deleted_count' => $deletedCount
        ]);
    }
}
