<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    /**
     * Submit contact form (Frontend)
     */
    public function submit(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'service' => 'nullable|string|max:255',
                'budget' => 'nullable|string|max:255',
                'message' => 'required|string|max:5000',
            ]);

            $contactMessage = ContactMessage::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully! We will get back to you within 24 hours.',
                'data' => $contactMessage
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending your message. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get all contact messages (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = ContactMessage::recent();

        // Filter by read status
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'unread') {
                $query->unread();
            } elseif ($status === 'read') {
                $query->read();
            }
            // If status is empty or any other value, show all messages
        }

        // Legacy support for unread_only parameter
        if ($request->has('unread_only') && $request->unread_only) {
            $query->unread();
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Get single contact message (Admin)
     */
    public function show(ContactMessage $contactMessage): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $contactMessage
        ]);
    }

    /**
     * Mark message as read (Admin)
     */
    public function markAsRead(ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
            'data' => $contactMessage->fresh()
        ]);
    }

    /**
     * Delete contact message (Admin)
     */
    public function destroy(ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    /**
     * Get contact messages statistics (Admin)
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::unread()->count(),
            'today' => ContactMessage::whereDate('created_at', now()->toDateString())->count(),
            'this_week' => ContactMessage::whereBetween('created_at', [
                now()->startOfWeek()->toDateTimeString(),
                now()->endOfWeek()->toDateTimeString()
            ])->count(),
            'this_month' => ContactMessage::whereYear('created_at', now()->year)
                                        ->whereMonth('created_at', now()->month)
                                        ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
