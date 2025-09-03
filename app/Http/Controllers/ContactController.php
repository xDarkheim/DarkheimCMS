<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    /**
     * Submit contact form (Frontend)
     */
    public function submit(Request $request): JsonResponse
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'service' => 'nullable|string|max:255',
                'budget' => 'nullable|string|max:255',
                'message' => 'required|string|max:5000',
                'message_type' => 'nullable|string|in:general,job_application,partnership',
                'position_interest' => 'nullable|string|max:255',
                'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
                'portfolio_url' => 'nullable|url|max:500',
                'experience_summary' => 'nullable|string|max:2000',
                'availability' => 'nullable|string|max:100',
                'salary_expectation' => 'nullable|numeric|min:0|max:999999.99',
            ];

            $validated = $request->validate($rules);

            // Set default message type if not provided
            if (!isset($validated['message_type'])) {
                $validated['message_type'] = 'general';
            }

            // Handle resume file upload
            if ($request->hasFile('resume_file')) {
                $file = $request->file('resume_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('resumes', $filename, 'public');
                $validated['resume_file'] = $path;
            }

            $contactMessage = ContactMessage::create($validated);

            $responseMessage = $validated['message_type'] === 'job_application'
                ? 'Your job application has been submitted successfully! We will review your resume and get back to you soon.'
                : 'Your message has been sent successfully! We will get back to you within 24 hours.';

            return response()->json([
                'success' => true,
                'message' => $responseMessage,
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

        // Filter by message type
        if ($request->has('message_type') && $request->message_type) {
            $query->byMessageType($request->message_type);
        }

        // Filter by read status
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'unread') {
                $query->unread();
            } elseif ($status === 'read') {
                $query->read();
            }
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
                  ->orWhere('position_interest', 'like', "%{$search}%")
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
        $wasRead = $contactMessage->is_read;
        $contactMessage->markAsRead();

        // Log marking message as read
        if (!$wasRead) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'marked_read',
                'model_type' => 'App\\Models\\ContactMessage',
                'model_id' => $contactMessage->id,
                'description' => "Marked contact message as read from {$contactMessage->name} ({$contactMessage->email})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'changes' => [
                    'is_read' => [
                        'old' => false,
                        'new' => true
                    ],
                    'read_at' => [
                        'old' => null,
                        'new' => now()
                    ]
                ],
                'severity' => 'info'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
            'data' => $contactMessage->fresh()
        ]);
    }

    /**
     * Delete contact message with resume cleanup (Admin)
     */
    public function destroy(ContactMessage $contactMessage): JsonResponse
    {
        $messageData = $contactMessage->toArray();
        $senderName = $contactMessage->name;
        $senderEmail = $contactMessage->email;
        $messageId = $contactMessage->id;
        $messageType = $contactMessage->message_type;

        // Delete resume file if exists
        if ($contactMessage->resume_file) {
            Storage::disk('public')->delete($contactMessage->resume_file);
        }

        $contactMessage->delete();

        // Log contact message deletion
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => 'App\\Models\\ContactMessage',
            'model_id' => $messageId,
            'description' => "Deleted {$messageType} message from {$senderName} ({$senderEmail})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $messageData,
            'severity' => 'warning'
        ]);

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
            'job_applications' => ContactMessage::jobApplications()->count(),
            'general_messages' => ContactMessage::generalMessages()->count(),
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

    /**
     * Download resume file (Admin)
     */
    public function downloadResume(ContactMessage $contactMessage): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
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
                'message' => 'Resume file not found'
            ], 404);
        }

        // Log resume download
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'resume_downloaded',
            'model_type' => 'App\\Models\\ContactMessage',
            'model_id' => $contactMessage->id,
            'description' => "Downloaded resume from {$contactMessage->name} ({$contactMessage->email})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => [
                'resume_file' => $contactMessage->resume_file,
                'applicant_name' => $contactMessage->name,
                'position_interest' => $contactMessage->position_interest
            ],
            'severity' => 'info'
        ]);

        return response()->download($filePath);
    }
}
