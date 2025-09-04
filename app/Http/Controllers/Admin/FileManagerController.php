<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileManagerController extends Controller
{
    /**
     * @var array<string, array<string>>
     */
    protected array $allowedExtensions = [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'documents' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
        'archives' => ['zip', 'rar', '7z'],
        'videos' => ['mp4', 'avi', 'mov', 'wmv', 'webm'],
    ];

    protected int $maxFileSize = 10240; // 10MB in KB

    /**
     * Get files and folders
     */
    public function index(Request $request): JsonResponse
    {
        $path = $request->get('path', '');
        $type = $request->get('type', 'all'); // all, images, documents, etc.

        try {
            $files = Storage::disk('public')->files($path);
            $directories = Storage::disk('public')->directories($path);

            $result = [
                'current_path' => $path,
                'directories' => array_map(function($dir) {
                    return [
                        'name' => basename($dir),
                        'path' => $dir,
                        'type' => 'directory'
                    ];
                }, $directories),
                'files' => []
            ];

            foreach ($files as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $fileType = $this->getFileType($extension);

                // Filter by type if specified
                if ($type !== 'all' && $fileType !== $type) {
                    continue;
                }

                $fileInfo = [
                    'name' => basename($file),
                    'path' => $file,
                    'url' => Storage::url($file),
                    'size' => Storage::disk('public')->size($file),
                    'type' => $fileType,
                    'extension' => $extension,
                    'modified' => Storage::disk('public')->lastModified($file),
                    'is_image' => $fileType === 'images'
                ];

                // Add image dimensions if it's an image
                if ($fileType === 'images' && $extension !== 'svg') {
                    try {
                        $fullPath = Storage::disk('public')->path($file);
                        $imageSize = getimagesize($fullPath);
                        if ($imageSize) {
                            $fileInfo['width'] = $imageSize[0];
                            $fileInfo['height'] = $imageSize[1];
                        }
                    } catch (\Exception $e) {
                        // Ignore errors getting image size
                    }
                }

                $result['files'][] = $fileInfo;
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload files
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'files.*' => 'required|file|max:' . $this->maxFileSize,
            'path' => 'string|nullable'
        ]);

        $path = $request->get('path', '');
        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                // Validate file extension
                $extension = strtolower($file->getClientOriginalExtension());
                if (!$this->isAllowedExtension($extension)) {
                    $errors[] = "File {$file->getClientOriginalName()}: Unsupported file type";
                    continue;
                }

                // Generate unique filename
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileName = Str::slug($originalName) . '_' . time() . '.' . $extension;

                // Store file
                $storedPath = $file->storeAs($path, $fileName, 'public');

                // Create thumbnail for images
                if ($this->getFileType($extension) === 'images' && $extension !== 'svg') {
                    $this->createThumbnail($storedPath);
                }

                $uploadedFiles[] = [
                    'name' => $fileName,
                    'path' => $storedPath,
                    'url' => Storage::url($storedPath),
                    'size' => $file->getSize(),
                    'type' => $this->getFileType($extension)
                ];

            } catch (\Exception $e) {
                $errors[] = "File {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        // Log file upload activity
        if (count($uploadedFiles) > 0) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'file_upload',
                'description' => "Uploaded " . count($uploadedFiles) . " file(s) to path: /" . $path,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => [
                    'uploaded_files' => array_column($uploadedFiles, 'name'),
                    'path' => $path,
                    'total_size' => array_sum(array_column($uploadedFiles, 'size'))
                ],
                'severity' => 'info'
            ]);
        }

        return response()->json([
            'success' => count($uploadedFiles) > 0,
            'uploaded' => $uploadedFiles,
            'errors' => $errors,
            'message' => count($uploadedFiles) . ' files uploaded successfully'
        ]);
    }

    /**
     * Create directory
     */
    public function createDirectory(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'string|nullable'
        ]);

        $basePath = $request->get('path', '');
        $dirName = Str::slug($request->name);
        $fullPath = $basePath ? $basePath . '/' . $dirName : $dirName;

        try {
            if (Storage::disk('public')->exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Directory already exists'
                ], 400);
            }

            Storage::disk('public')->makeDirectory($fullPath);

            // Log directory creation
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'directory_created',
                'description' => "Created directory: /{$fullPath}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => [
                    'directory_name' => $dirName,
                    'full_path' => $fullPath,
                    'parent_path' => $basePath
                ],
                'severity' => 'info'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Directory created successfully',
                'data' => [
                    'name' => $dirName,
                    'path' => $fullPath
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create directory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete files or directories
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'string'
        ]);

        $deleted = [];
        $errors = [];

        foreach ($request->items as $item) {
            try {
                if (Storage::disk('public')->exists($item)) {
                    $itemType = Storage::disk('public')->directoryExists($item) ? 'directory' : 'file';
                    $itemName = basename($item);

                    if ($itemType === 'directory') {
                        Storage::disk('public')->deleteDirectory($item);
                    } else {
                        Storage::disk('public')->delete($item);
                        // Also delete thumbnail if exists
                        $thumbnailPath = 'thumbnails/' . $item;
                        if (Storage::disk('public')->exists($thumbnailPath)) {
                            Storage::disk('public')->delete($thumbnailPath);
                        }
                    }
                    $deleted[] = $item;
                } else {
                    $errors[] = "Item not found: $item";
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to delete $item: " . $e->getMessage();
            }
        }

        // Log file deletion activity
        if (count($deleted) > 0) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'file_delete',
                'description' => "Deleted " . count($deleted) . " item(s): " . implode(', ', array_map('basename', $deleted)),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changes' => [
                    'deleted_items' => $deleted,
                    'total_count' => count($deleted)
                ],
                'severity' => 'warning'
            ]);
        }

        return response()->json([
            'success' => count($deleted) > 0,
            'deleted' => $deleted,
            'errors' => $errors,
            'message' => count($deleted) . ' items deleted successfully'
        ]);
    }

    /**
     * Get file type based on extension
     */
    private function getFileType(string $extension): string
    {
        foreach ($this->allowedExtensions as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type;
            }
        }
        return 'other';
    }

    /**
     * Check if extension is allowed
     */
    private function isAllowedExtension(string $extension): bool
    {
        foreach ($this->allowedExtensions as $extensions) {
            if (in_array($extension, $extensions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Create thumbnail for images
     */
    private function createThumbnail(string $filePath): void
    {
        try {
            $manager = new ImageManager(new Driver());
            $fullPath = Storage::disk('public')->path($filePath);

            if (!file_exists($fullPath)) {
                return;
            }

            $image = $manager->read($fullPath);
            $image->scale(width: 300);

            $thumbnailPath = 'thumbnails/' . $filePath;
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

            // Create directory if it doesn't exist
            $thumbnailDir = dirname($thumbnailFullPath);
            if (!is_dir($thumbnailDir)) {
                if (!mkdir($thumbnailDir, 0755, true) && !is_dir($thumbnailDir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $thumbnailDir));
                }
            }

            $image->save($thumbnailFullPath);

        } catch (\Exception $e) {
            // Log error but don't fail the upload
            \Log::warning('Failed to create thumbnail for ' . $filePath . ': ' . $e->getMessage());
        }
    }
}
