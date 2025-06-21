<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use LaravelBuilder\VisualBuilder\Services\BackupService;
use LaravelBuilder\VisualBuilder\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class BackupController extends Controller
{
    protected BackupService $backupService;
    protected ?TelegramBotService $telegramService;

    public function __construct(BackupService $backupService, ?TelegramBotService $telegramService = null)
    {
        $this->backupService = $backupService;
        $this->telegramService = $telegramService;
        
        if ($this->telegramService) {
            $this->backupService->setTelegramService($this->telegramService);
        }
    }

    /**
     * Create a new backup
     */
    public function create(Request $request): JsonResponse
    {
        $config = $request->validate([
            'backup_path' => 'string',
            'exclude_paths' => 'array',
            'max_backups' => 'integer|min:1|max:100',
            'compress_backups' => 'boolean',
            'notify_on_completion' => 'boolean'
        ]);

        if (!empty($config)) {
            $this->backupService->setConfig($config);
        }

        $result = $this->backupService->createBackup();

        return response()->json($result);
    }

    /**
     * Get list of backups
     */
    public function index(): JsonResponse
    {
        $backups = $this->backupService->getBackupList();

        return response()->json([
            'success' => true,
            'data' => $backups,
            'count' => count($backups)
        ]);
    }

    /**
     * Get backup details
     */
    public function show(string $backupName): JsonResponse
    {
        $backups = $this->backupService->getBackupList();
        
        $backup = collect($backups)->firstWhere('name', $backupName);
        
        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $backup
        ]);
    }

    /**
     * Download backup
     */
    public function download(string $backupName): JsonResponse
    {
        $backups = $this->backupService->getBackupList();
        
        $backup = collect($backups)->firstWhere('name', $backupName);
        
        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup not found'
            ], 404);
        }

        if (!file_exists($backup['path'])) {
            return response()->json([
                'success' => false,
                'message' => 'Backup file not found'
            ], 404);
        }

        // In a real implementation, you'd return a file download response
        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => '/api/backups/' . $backupName . '/download-file',
                'file_size' => $backup['size'],
                'file_name' => $backup['name']
            ]
        ]);
    }

    /**
     * Delete backup
     */
    public function destroy(string $backupName): JsonResponse
    {
        $backups = $this->backupService->getBackupList();
        
        $backup = collect($backups)->firstWhere('name', $backupName);
        
        if (!$backup) {
            return response()->json([
                'success' => false,
                'message' => 'Backup not found'
            ], 404);
        }

        if (file_exists($backup['path'])) {
            $deleted = unlink($backup['path']);
            
            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? 'Backup deleted successfully' : 'Failed to delete backup'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Backup file not found'
        ], 404);
    }

    /**
     * Get backup statistics
     */
    public function stats(): JsonResponse
    {
        $backups = $this->backupService->getBackupList();
        
        $totalSize = collect($backups)->sum('size');
        $totalCount = count($backups);
        $oldestBackup = collect($backups)->sortBy('created_at')->first();
        $newestBackup = collect($backups)->sortByDesc('created_at')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_backups' => $totalCount,
                'total_size' => $totalSize,
                'total_size_formatted' => $this->formatBytes($totalSize),
                'oldest_backup' => $oldestBackup ? $oldestBackup['created_at'] : null,
                'newest_backup' => $newestBackup ? $newestBackup['created_at'] : null,
                'average_size' => $totalCount > 0 ? $totalSize / $totalCount : 0,
                'average_size_formatted' => $totalCount > 0 ? $this->formatBytes($totalSize / $totalCount) : '0 B'
            ]
        ]);
    }

    /**
     * Configure backup settings
     */
    public function configure(Request $request): JsonResponse
    {
        $config = $request->validate([
            'backup_path' => 'string',
            'exclude_paths' => 'array',
            'max_backups' => 'integer|min:1|max:100',
            'compress_backups' => 'boolean',
            'notify_on_completion' => 'boolean'
        ]);

        $this->backupService->setConfig($config);

        return response()->json([
            'success' => true,
            'message' => 'Backup configuration updated successfully'
        ]);
    }

    /**
     * Test backup configuration
     */
    public function test(): JsonResponse
    {
        try {
            // Create a small test backup
            $testConfig = [
                'max_backups' => 1,
                'compress_backups' => false,
                'notify_on_completion' => false
            ];
            
            $this->backupService->setConfig($testConfig);
            $result = $this->backupService->createBackup();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Backup test completed successfully' : 'Backup test failed',
                'details' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup test failed: ' . $e->getMessage()
            ], 500);
        }
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