<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InformationBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'information';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/information-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/information-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create information service
        $this->buildInformationService($config);

        // Create cloud storage service
        $this->buildCloudStorageService($config);

        // Create backup service
        $this->buildBackupService($config);

        // Create sync service
        $this->buildSyncService($config);

        return $this->output;
    }

    protected function buildInformationService(array $config): void
    {
        $serviceName = $this->getServiceName($config['name']);
        $servicePath = app_path("Services/Information/{$serviceName}.php");
        $serviceStub = $this->getStub('information_service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'methods' => $this->getInformationMethods($config),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function buildCloudStorageService(array $config): void
    {
        $serviceName = $this->getCloudStorageName($config['name']);
        $servicePath = app_path("Services/Information/CloudStorage/{$serviceName}.php");
        $serviceStub = $this->getStub('cloud_storage_service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'methods' => $this->getCloudStorageMethods($config),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function buildBackupService(array $config): void
    {
        $serviceName = $this->getBackupName($config['name']);
        $servicePath = app_path("Services/Information/Backup/{$serviceName}.php");
        $serviceStub = $this->getStub('backup_service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'methods' => $this->getBackupMethods($config),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function buildSyncService(array $config): void
    {
        $serviceName = $this->getSyncName($config['name']);
        $servicePath = app_path("Services/Information/Sync/{$serviceName}.php");
        $serviceStub = $this->getStub('sync_service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'methods' => $this->getSyncMethods($config),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function getInformationMethods(array $config): string
    {
        $methods = [
            'getSystemInfo' => $this->getSystemInfoMethod(),
            'getStorageInfo' => $this->getStorageInfoMethod(),
            'getBackupInfo' => $this->getBackupInfoMethod(),
            'getSyncInfo' => $this->getSyncInfoMethod(),
        ];

        return implode("\n\n    ", $methods);
    }

    protected function getCloudStorageMethods(array $config): string
    {
        $methods = [
            'connectGoogleDrive' => $this->getGoogleDriveMethod(),
            'connectMega' => $this->getMegaMethod(),
            'connectDropbox' => $this->getDropboxMethod(),
            'uploadFile' => $this->getUploadFileMethod(),
            'downloadFile' => $this->getDownloadFileMethod(),
            'listFiles' => $this->getListFilesMethod(),
        ];

        return implode("\n\n    ", $methods);
    }

    protected function getBackupMethods(array $config): string
    {
        $methods = [
            'createBackup' => $this->getCreateBackupMethod(),
            'restoreBackup' => $this->getRestoreBackupMethod(),
            'listBackups' => $this->getListBackupsMethod(),
            'deleteBackup' => $this->getDeleteBackupMethod(),
        ];

        return implode("\n\n    ", $methods);
    }

    protected function getSyncMethods(array $config): string
    {
        $methods = [
            'syncToCloud' => $this->getSyncToCloudMethod(),
            'syncFromCloud' => $this->getSyncFromCloudMethod(),
            'getSyncStatus' => $this->getSyncStatusMethod(),
        ];

        return implode("\n\n    ", $methods);
    }

    protected function getServiceName(string $model): string
    {
        return $model . 'InformationService';
    }

    protected function getCloudStorageName(string $model): string
    {
        return $model . 'CloudStorageService';
    }

    protected function getBackupName(string $model): string
    {
        return $model . 'BackupService';
    }

    protected function getSyncName(string $model): string
    {
        return $model . 'SyncService';
    }

    // Method implementations
    protected function getSystemInfoMethod(): string
    {
        return <<<'PHP'
    public function getSystemInfo()
    {
        return [
            'os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_usage' => memory_get_usage(true),
            'disk_free_space' => disk_free_space('/'),
            'disk_total_space' => disk_total_space('/')
        ];
    }
PHP;
    }

    protected function getStorageInfoMethod(): string
    {
        return <<<'PHP'
    public function getStorageInfo()
    {
        $cloudStorage = new CloudStorageService();
        return [
            'local' => [
                'free_space' => disk_free_space(storage_path()),
                'total_space' => disk_total_space(storage_path())
            ],
            'cloud' => $cloudStorage->getStorageInfo()
        ];
    }
PHP;
    }

    protected function getBackupInfoMethod(): string
    {
        return <<<'PHP'
    public function getBackupInfo()
    {
        $backupService = new BackupService();
        return [
            'last_backup' => $backupService->getLastBackupTime(),
            'backup_count' => $backupService->getBackupCount(),
            'backup_size' => $backupService->getTotalBackupSize()
        ];
    }
PHP;
    }

    protected function getSyncInfoMethod(): string
    {
        return <<<'PHP'
    public function getSyncInfo()
    {
        $syncService = new SyncService();
        return [
            'last_sync' => $syncService->getLastSyncTime(),
            'sync_status' => $syncService->getSyncStatus(),
            'pending_changes' => $syncService->getPendingChanges()
        ];
    }
PHP;
    }

    protected function getGoogleDriveMethod(): string
    {
        return <<<'PHP'
    public function connectGoogleDrive()
    {
        $client = new \Google_Client();
        $client->setAuthConfig(storage_path('app/google-credentials.json'));
        $client->addScope(\Google_Service_Drive::DRIVE);
        
        return $client;
    }
PHP;
    }

    protected function getMegaMethod(): string
    {
        return <<<'PHP'
    public function connectMega()
    {
        $mega = new \MegaClient();
        $mega->login(config('services.mega.email'), config('services.mega.password'));
        
        return $mega;
    }
PHP;
    }

    protected function getDropboxMethod(): string
    {
        return <<<'PHP'
    public function connectDropbox()
    {
        $app = new \Spatie\Dropbox\Client(config('services.dropbox.access_token'));
        return $app;
    }
PHP;
    }

    protected function getUploadFileMethod(): string
    {
        return <<<'PHP'
    public function uploadFile($file, $destination)
    {
        $client = $this->getClient();
        return $client->uploadFile($file, $destination);
    }
PHP;
    }

    protected function getDownloadFileMethod(): string
    {
        return <<<'PHP'
    public function downloadFile($file, $destination)
    {
        $client = $this->getClient();
        return $client->downloadFile($file, $destination);
    }
PHP;
    }

    protected function getListFilesMethod(): string
    {
        return <<<'PHP'
    public function listFiles($path = '/')
    {
        $client = $this->getClient();
        return $client->listFiles($path);
    }
PHP;
    }

    protected function getCreateBackupMethod(): string
    {
        return <<<'PHP'
    public function createBackup()
    {
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $path
        );
        
        exec($command);
        
        return $path;
    }
PHP;
    }

    protected function getRestoreBackupMethod(): string
    {
        return <<<'PHP'
    public function restoreBackup($backupFile)
    {
        if (!file_exists($backupFile)) {
            throw new \Exception('Backup file not found');
        }
        
        $command = sprintf(
            'mysql -u%s -p%s %s < %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $backupFile
        );
        
        exec($command);
        
        return true;
    }
PHP;
    }

    protected function getListBackupsMethod(): string
    {
        return <<<'PHP'
    public function listBackups()
    {
        $path = storage_path('app/backups');
        $files = glob($path . '/*.sql');
        
        return array_map(function($file) {
            return [
                'name' => basename($file),
                'size' => filesize($file),
                'created_at' => filemtime($file)
            ];
        }, $files);
    }
PHP;
    }

    protected function getDeleteBackupMethod(): string
    {
        return <<<'PHP'
    public function deleteBackup($backupFile)
    {
        $path = storage_path('app/backups/' . $backupFile);
        
        if (file_exists($path)) {
            unlink($path);
            return true;
        }
        
        return false;
    }
PHP;
    }

    protected function getSyncToCloudMethod(): string
    {
        return <<<'PHP'
    public function syncToCloud()
    {
        $cloudStorage = new CloudStorageService();
        $backupService = new BackupService();
        
        $backupFile = $backupService->createBackup();
        return $cloudStorage->uploadFile($backupFile, 'backups/' . basename($backupFile));
    }
PHP;
    }

    protected function getSyncFromCloudMethod(): string
    {
        return <<<'PHP'
    public function syncFromCloud($backupFile)
    {
        $cloudStorage = new CloudStorageService();
        $backupService = new BackupService();
        
        $localPath = storage_path('app/backups/' . basename($backupFile));
        $cloudStorage->downloadFile('backups/' . $backupFile, $localPath);
        
        return $backupService->restoreBackup($localPath);
    }
PHP;
    }

    protected function getSyncStatusMethod(): string
    {
        return <<<'PHP'
    public function getSyncStatus()
    {
        $cloudStorage = new CloudStorageService();
        $backupService = new BackupService();
        
        $localBackups = $backupService->listBackups();
        $cloudBackups = $cloudStorage->listFiles('backups');
        
        return [
            'local_count' => count($localBackups),
            'cloud_count' => count($cloudBackups),
            'last_sync' => $this->getLastSyncTime()
        ];
    }
PHP;
    }
} 