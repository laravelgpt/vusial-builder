<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use LaravelBuilder\VisualBuilder\Services\CloudStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CloudStorageController extends Controller
{
    protected CloudStorageService $cloudStorageService;

    public function __construct(CloudStorageService $cloudStorageService)
    {
        $this->cloudStorageService = $cloudStorageService;
    }

    /**
     * Configure cloud storage
     */
    public function configure(Request $request): JsonResponse
    {
        $config = $request->validate([
            'driver' => 'required|string|in:local,s3,gcs,azure,dropbox',
            'path' => 'string',
            's3' => 'array',
            's3.bucket' => 'string',
            's3.region' => 'string',
            's3.key' => 'string',
            's3.secret' => 'string',
            'gcs' => 'array',
            'gcs.bucket' => 'string',
            'gcs.project_id' => 'string',
            'gcs.key_file' => 'string',
            'azure' => 'array',
            'azure.container' => 'string',
            'azure.account' => 'string',
            'azure.key' => 'string',
            'dropbox' => 'array',
            'dropbox.access_token' => 'string',
            'dropbox.app_key' => 'string',
            'dropbox.app_secret' => 'string'
        ]);

        $this->cloudStorageService->setConfig($config);

        return response()->json([
            'success' => true,
            'message' => 'Cloud storage configuration updated successfully'
        ]);
    }

    /**
     * Upload file to cloud storage
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file',
            'remote_path' => 'required|string',
            'options' => 'array'
        ]);

        $file = $request->file('file');
        $remotePath = $request->input('remote_path');
        $options = $request->input('options', []);

        $result = $this->cloudStorageService->upload(
            $file->getPathname(),
            $remotePath,
            $options
        );

        return response()->json($result);
    }

    /**
     * Download file from cloud storage
     */
    public function download(Request $request): JsonResponse
    {
        $request->validate([
            'remote_path' => 'required|string',
            'local_path' => 'required|string',
            'options' => 'array'
        ]);

        $remotePath = $request->input('remote_path');
        $localPath = $request->input('local_path');
        $options = $request->input('options', []);

        $result = $this->cloudStorageService->download(
            $remotePath,
            $localPath,
            $options
        );

        return response()->json($result);
    }

    /**
     * Delete file from cloud storage
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'remote_path' => 'required|string'
        ]);

        $remotePath = $request->input('remote_path');

        $result = $this->cloudStorageService->delete($remotePath);

        return response()->json($result);
    }

    /**
     * Get file URL
     */
    public function getUrl(Request $request): JsonResponse
    {
        $request->validate([
            'remote_path' => 'required|string'
        ]);

        $remotePath = $request->input('remote_path');
        $url = $this->cloudStorageService->getUrl($remotePath);

        return response()->json([
            'success' => $url !== null,
            'data' => [
                'url' => $url,
                'remote_path' => $remotePath
            ]
        ]);
    }

    /**
     * Check if file exists
     */
    public function exists(Request $request): JsonResponse
    {
        $request->validate([
            'remote_path' => 'required|string'
        ]);

        $remotePath = $request->input('remote_path');
        $exists = $this->cloudStorageService->exists($remotePath);

        return response()->json([
            'success' => true,
            'data' => [
                'exists' => $exists,
                'remote_path' => $remotePath
            ]
        ]);
    }

    /**
     * Get file size
     */
    public function getSize(Request $request): JsonResponse
    {
        $request->validate([
            'remote_path' => 'required|string'
        ]);

        $remotePath = $request->input('remote_path');
        $size = $this->cloudStorageService->getSize($remotePath);

        return response()->json([
            'success' => $size !== null,
            'data' => [
                'size' => $size,
                'size_formatted' => $size ? $this->formatBytes($size) : null,
                'remote_path' => $remotePath
            ]
        ]);
    }

    /**
     * Test cloud storage connection
     */
    public function test(): JsonResponse
    {
        try {
            // Create a test file
            $testContent = 'Test file for cloud storage connection';
            $testFile = tempnam(sys_get_temp_dir(), 'cloud_storage_test_');
            file_put_contents($testFile, $testContent);

            // Upload test file
            $remotePath = 'test/connection_test.txt';
            $uploadResult = $this->cloudStorageService->upload($testFile, $remotePath);

            if (!$uploadResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload test failed: ' . ($uploadResult['error'] ?? 'Unknown error')
                ]);
            }

            // Check if file exists
            $exists = $this->cloudStorageService->exists($remotePath);

            // Get file size
            $size = $this->cloudStorageService->getSize($remotePath);

            // Get file URL
            $url = $this->cloudStorageService->getUrl($remotePath);

            // Delete test file
            $deleteResult = $this->cloudStorageService->delete($remotePath);

            // Clean up local test file
            unlink($testFile);

            return response()->json([
                'success' => true,
                'message' => 'Cloud storage connection test completed successfully',
                'data' => [
                    'upload_success' => $uploadResult['success'],
                    'file_exists' => $exists,
                    'file_size' => $size,
                    'file_url' => $url,
                    'delete_success' => $deleteResult['success']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cloud storage connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cloud storage statistics
     */
    public function stats(): JsonResponse
    {
        // This would typically connect to the cloud storage and get actual statistics
        // For now, we'll return basic information
        return response()->json([
            'success' => true,
            'data' => [
                'driver' => 'local', // This would be dynamic based on configuration
                'status' => 'connected',
                'available_space' => 'unknown',
                'total_files' => 'unknown',
                'last_sync' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Sync files to cloud storage
     */
    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'local_path' => 'required|string',
            'remote_path' => 'required|string',
            'recursive' => 'boolean'
        ]);

        $localPath = $request->input('local_path');
        $remotePath = $request->input('remote_path');
        $recursive = $request->input('recursive', false);

        if (!is_dir($localPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Local path is not a directory'
            ], 400);
        }

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        if ($recursive) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($localPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $relativePath = str_replace($localPath . '/', '', $file->getPathname());
                    $targetPath = $remotePath . '/' . $relativePath;

                    $result = $this->cloudStorageService->upload($file->getPathname(), $targetPath);
                    $results[] = [
                        'file' => $relativePath,
                        'success' => $result['success'],
                        'error' => $result['error'] ?? null
                    ];

                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                }
            }
        } else {
            $files = glob($localPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $relativePath = basename($file);
                    $targetPath = $remotePath . '/' . $relativePath;

                    $result = $this->cloudStorageService->upload($file, $targetPath);
                    $results[] = [
                        'file' => $relativePath,
                        'success' => $result['success'],
                        'error' => $result['error'] ?? null
                    ];

                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                }
            }
        }

        return response()->json([
            'success' => $errorCount === 0,
            'data' => [
                'total_files' => count($results),
                'successful_uploads' => $successCount,
                'failed_uploads' => $errorCount,
                'results' => $results
            ]
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 