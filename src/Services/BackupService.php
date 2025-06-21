<?php

namespace LaravelBuilder\VisualBuilder\Services;

class BackupService
{
    protected string $backupPath;
    protected array $excludePaths;
    protected int $maxBackups;
    protected bool $compressBackups;
    protected bool $notifyOnCompletion;
    protected ?TelegramBotService $telegramService;

    public function __construct()
    {
        $this->backupPath = dirname(__DIR__, 4) . '/storage/backups';
        $this->excludePaths = [
            'node_modules',
            'vendor',
            '.git',
            'storage/logs',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views'
        ];
        $this->maxBackups = 10;
        $this->compressBackups = true;
        $this->notifyOnCompletion = true;
        $this->telegramService = null;
    }

    /**
     * Set configuration
     */
    public function setConfig(array $config): void
    {
        $this->backupPath = $config['backup_path'] ?? dirname(__DIR__, 4) . '/storage/backups';
        $this->excludePaths = $config['exclude_paths'] ?? $this->excludePaths;
        $this->maxBackups = $config['max_backups'] ?? 10;
        $this->compressBackups = $config['compress_backups'] ?? true;
        $this->notifyOnCompletion = $config['notify_on_completion'] ?? true;
    }

    /**
     * Set Telegram service for notifications
     */
    public function setTelegramService(TelegramBotService $telegramService): void
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Create a full backup
     */
    public function createBackup(): array
    {
        $startTime = microtime(true);
        $backupName = 'backup_' . date('Y-m-d_H-i-s');
        $backupDir = $this->backupPath . '/' . $backupName;
        
        try {
            // Create backup directory
            if (!is_dir($this->backupPath)) {
                mkdir($this->backupPath, 0755, true);
            }

            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $results = [
                'success' => true,
                'backup_name' => $backupName,
                'backup_path' => $backupDir,
                'files_backed_up' => 0,
                'database_backed_up' => false,
                'compressed' => false,
                'size' => 0,
                'duration' => 0,
                'errors' => []
            ];

            // Backup files
            $fileResults = $this->backupFiles($backupDir);
            $results['files_backed_up'] = $fileResults['count'];
            $results['errors'] = array_merge($results['errors'], $fileResults['errors']);

            // Backup database
            $dbResults = $this->backupDatabase($backupDir);
            $results['database_backed_up'] = $dbResults['success'];
            $results['errors'] = array_merge($results['errors'], $dbResults['errors']);

            // Compress backup
            if ($this->compressBackups) {
                $compressResults = $this->compressBackup($backupDir);
                $results['compressed'] = $compressResults['success'];
                $results['size'] = $compressResults['size'];
                $results['errors'] = array_merge($results['errors'], $compressResults['errors']);
            }

            // Clean old backups
            $this->cleanOldBackups();

            $results['duration'] = round(microtime(true) - $startTime, 2);

            // Send notification
            if ($this->notifyOnCompletion && $this->telegramService) {
                $this->sendBackupNotification($results);
            }

            return $results;

        } catch (\Exception $e) {
            $results = [
                'success' => false,
                'backup_name' => $backupName,
                'backup_path' => $backupDir,
                'files_backed_up' => 0,
                'database_backed_up' => false,
                'compressed' => false,
                'size' => 0,
                'duration' => round(microtime(true) - $startTime, 2),
                'errors' => [$e->getMessage()]
            ];

            if ($this->notifyOnCompletion && $this->telegramService) {
                $this->sendBackupNotification($results);
            }

            return $results;
        }
    }

    /**
     * Backup application files
     */
    protected function backupFiles(string $backupDir): array
    {
        $results = ['count' => 0, 'errors' => []];
        $sourceDir = dirname(__DIR__, 4); // Laravel root directory
        $filesBackupDir = $backupDir . '/files';

        try {
            if (!is_dir($filesBackupDir)) {
                mkdir($filesBackupDir, 0755, true);
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                $relativePath = str_replace($sourceDir . '/', '', $file->getPathname());
                
                // Skip excluded paths
                if ($this->shouldExcludePath($relativePath)) {
                    continue;
                }

                $targetPath = $filesBackupDir . '/' . $relativePath;

                if ($file->isDir()) {
                    if (!is_dir($targetPath)) {
                        mkdir($targetPath, 0755, true);
                    }
                } else {
                    $targetDir = dirname($targetPath);
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    
                    if (copy($file->getPathname(), $targetPath)) {
                        $results['count']++;
                    } else {
                        $results['errors'][] = "Failed to copy: {$relativePath}";
                    }
                }
            }

        } catch (\Exception $e) {
            $results['errors'][] = "File backup error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Backup database
     */
    protected function backupDatabase(string $backupDir): array
    {
        $results = ['success' => false, 'errors' => []];
        $dbBackupDir = $backupDir . '/database';

        try {
            if (!is_dir($dbBackupDir)) {
                mkdir($dbBackupDir, 0755, true);
            }

            // For now, we'll use a simple approach without Laravel config
            // In a real implementation, you'd get this from Laravel's config
            $connection = 'mysql'; // Default
            $config = [
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => 3306,
                'database' => 'laravel',
                'username' => 'root',
                'password' => ''
            ];

            $filename = "database_{$connection}_" . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $dbBackupDir . '/' . $filename;

            switch ($config['driver']) {
                case 'mysql':
                    $command = $this->buildMysqlDumpCommand($config, $filepath);
                    break;
                case 'pgsql':
                    $command = $this->buildPostgresDumpCommand($config, $filepath);
                    break;
                case 'sqlite':
                    $command = $this->buildSqliteDumpCommand($config, $filepath);
                    break;
                default:
                    $results['errors'][] = "Unsupported database driver: {$config['driver']}";
                    return $results;
            }

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($filepath)) {
                $results['success'] = true;
            } else {
                $results['errors'][] = "Database backup failed: " . implode("\n", $output);
            }

        } catch (\Exception $e) {
            $results['errors'][] = "Database backup error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Build MySQL dump command
     */
    protected function buildMysqlDumpCommand(array $config, string $filepath): string
    {
        $command = 'mysqldump';
        
        if (isset($config['host'])) {
            $command .= " -h {$config['host']}";
        }
        
        if (isset($config['port'])) {
            $command .= " -P {$config['port']}";
        }
        
        if (isset($config['username'])) {
            $command .= " -u {$config['username']}";
        }
        
        if (isset($config['password'])) {
            $command .= " -p{$config['password']}";
        }
        
        $command .= " {$config['database']} > {$filepath}";
        
        return $command;
    }

    /**
     * Build PostgreSQL dump command
     */
    protected function buildPostgresDumpCommand(array $config, string $filepath): string
    {
        $command = 'pg_dump';
        
        if (isset($config['host'])) {
            $command .= " -h {$config['host']}";
        }
        
        if (isset($config['port'])) {
            $command .= " -p {$config['port']}";
        }
        
        if (isset($config['username'])) {
            $command .= " -U {$config['username']}";
        }
        
        $command .= " {$config['database']} > {$filepath}";
        
        return $command;
    }

    /**
     * Build SQLite dump command
     */
    protected function buildSqliteDumpCommand(array $config, string $filepath): string
    {
        $database = $config['database'];
        return "sqlite3 {$database} .dump > {$filepath}";
    }

    /**
     * Compress backup directory
     */
    protected function compressBackup(string $backupDir): array
    {
        $results = ['success' => false, 'size' => 0, 'errors' => []];
        $archivePath = $backupDir . '.tar.gz';

        try {
            $command = "tar -czf {$archivePath} -C " . dirname($backupDir) . " " . basename($backupDir);
            
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($archivePath)) {
                // Remove uncompressed directory
                $this->removeDirectory($backupDir);
                
                $results['success'] = true;
                $results['size'] = filesize($archivePath);
            } else {
                $results['errors'][] = "Compression failed: " . implode("\n", $output);
            }

        } catch (\Exception $e) {
            $results['errors'][] = "Compression error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Clean old backups
     */
    protected function cleanOldBackups(): void
    {
        $backups = glob($this->backupPath . '/backup_*');
        
        if (count($backups) <= $this->maxBackups) {
            return;
        }

        // Sort by modification time (oldest first)
        usort($backups, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        // Remove oldest backups
        $toRemove = array_slice($backups, 0, count($backups) - $this->maxBackups);
        
        foreach ($toRemove as $backup) {
            if (is_dir($backup)) {
                $this->removeDirectory($backup);
            } else {
                unlink($backup);
            }
        }
    }

    /**
     * Check if path should be excluded
     */
    protected function shouldExcludePath(string $path): bool
    {
        foreach ($this->excludePaths as $excludePath) {
            if (strpos($path, $excludePath) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove directory recursively
     */
    protected function removeDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        return rmdir($dir);
    }

    /**
     * Send backup notification
     */
    protected function sendBackupNotification(array $results): void
    {
        if (!$this->telegramService) {
            return;
        }

        $status = $results['success'] ? '✅ Completed' : '❌ Failed';
        $details = "Files: {$results['files_backed_up']}\n";
        $details .= "Database: " . ($results['database_backed_up'] ? 'Yes' : 'No') . "\n";
        $details .= "Compressed: " . ($results['compressed'] ? 'Yes' : 'No') . "\n";
        $details .= "Duration: {$results['duration']}s\n";
        
        if ($results['size'] > 0) {
            $details .= "Size: " . $this->formatBytes($results['size']) . "\n";
        }

        if (!empty($results['errors'])) {
            $details .= "Errors: " . implode(', ', array_slice($results['errors'], 0, 3));
            if (count($results['errors']) > 3) {
                $details .= " (+" . (count($results['errors']) - 3) . " more)";
            }
        }

        $this->telegramService->sendBackupNotification($status, $details);
    }

    /**
     * Get backup list
     */
    public function getBackupList(): array
    {
        $backups = [];
        $files = glob($this->backupPath . '/backup_*');

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                'is_compressed' => pathinfo($file, PATHINFO_EXTENSION) === 'gz'
            ];
        }

        // Sort by creation time (newest first)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $backups;
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