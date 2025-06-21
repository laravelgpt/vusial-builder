<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelBuilder\VisualBuilder\Services\BaseBuilder;

class DatabaseBackupBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'database-backup';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/database-backup-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/database-backup-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create backup service
        $this->buildBackupService($config);

        // Create cloud storage service
        $this->buildCloudStorageService($config);

        // Create scheduler service
        $this->buildSchedulerService($config);

        // Create notification service
        $this->buildNotificationService($config);

        return $this->output;
    }

    protected function buildBackupService(array $config): void
    {
        $serviceName = $this->getServiceName($config['name']);
        $servicePath = app_path("Services/DatabaseBackup/{$serviceName}.php");
        $serviceStub = $this->getStub('backup_service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'methods' => $this->getBackupMethods($config),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function buildCloudStorageService(array $config): void
    {
        $serviceName = $this->getCloudStorageName($config['name']);
        $servicePath = app_path("Services/DatabaseBackup/CloudStorage/{$serviceName}.php");
        $serviceStub = $this->getStub('cloud_storage_service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'methods' => $this->getCloudStorageMethods($config),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function buildSchedulerService(array $config): void
    {
        $serviceName = $this->getSchedulerName($config['name']);
        $servicePath = app_path("Services/DatabaseBackup/Scheduler/{$serviceName}.php");
        $serviceStub = $this->getStub('scheduler_service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'methods' => $this->getSchedulerMethods($config),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function buildNotificationService(array $config): void
    {
        $serviceName = $this->getNotificationName($config['name']);
        $servicePath = app_path("Services/DatabaseBackup/Notification/{$serviceName}.php");
        $serviceStub = $this->getStub('notification_service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'methods' => $this->getNotificationMethods($config),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function getBackupMethods(array $config): string
    {
        $methods = [
            'createBackup' => $this->getCreateBackupMethod(),
            'restoreBackup' => $this->getRestoreBackupMethod(),
            'listBackups' => $this->getListBackupsMethod(),
            'deleteBackup' => $this->getDeleteBackupMethod(),
            'compressBackup' => $this->getCompressBackupMethod(),
            'verifyBackup' => $this->getVerifyBackupMethod(),
        ];

        return implode("\n\n    ", $methods);
    }

    protected function getCloudStorageMethods(array $config): string
    {
        $methods = [
            'connectGoogleDrive' => $this->getGoogleDriveMethod(),
            'connectMega' => $this->getMegaMethod(),
            'connectDropbox' => $this->getDropboxMethod(),
            'uploadBackup' => $this->getUploadBackupMethod(),
            'downloadBackup' => $this->getDownloadBackupMethod(),
            'listCloudBackups' => $this->getListCloudBackupsMethod(),
        ];

        return implode("\n\n    ", $methods);
    }

    protected function getSchedulerMethods(array $config): string
    {
        $methods = [
            'scheduleBackup' => $this->getScheduleBackupMethod(),
            'getSchedule' => $this->getScheduleMethod(),
            'updateSchedule' => $this->getUpdateScheduleMethod(),
            'deleteSchedule' => $this->getDeleteScheduleMethod(),
        ];

        return implode("\n\n    ", $methods);
    }

    protected function getNotificationMethods(array $config): string
    {
        $methods = [
            'notifyBackupSuccess' => $this->getNotifyBackupSuccessMethod(),
            'notifyBackupFailure' => $this->getNotifyBackupFailureMethod(),
            'notifyStorageFull' => $this->getNotifyStorageFullMethod(),
        ];

        return implode("\n\n    ", $methods);
    }

    protected function getServiceName(string $model): string
    {
        return $model . 'BackupService';
    }

    protected function getCloudStorageName(string $model): string
    {
        return $model . 'CloudStorageService';
    }

    protected function getSchedulerName(string $model): string
    {
        return $model . 'SchedulerService';
    }

    protected function getNotificationName(string $model): string
    {
        return $model . 'NotificationService';
    }

    // Method implementations
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

    protected function getCompressBackupMethod(): string
    {
        return <<<'PHP'
    public function compressBackup($backupFile)
    {
        $path = storage_path('app/backups/' . $backupFile);
        $zipPath = $path . '.zip';
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $zip->addFile($path, basename($path));
            $zip->close();
            
            unlink($path);
            return $zipPath;
        }
        
        return false;
    }
PHP;
    }

    protected function getVerifyBackupMethod(): string
    {
        return <<<'PHP'
    public function verifyBackup($backupFile)
    {
        $path = storage_path('app/backups/' . $backupFile);
        
        if (!file_exists($path)) {
            return false;
        }
        
        $command = sprintf(
            'mysqlcheck -u%s -p%s %s < %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $path
        );
        
        exec($command, $output, $returnVar);
        
        return $returnVar === 0;
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

    protected function getUploadBackupMethod(): string
    {
        return <<<'PHP'
    public function uploadBackup($backupFile)
    {
        $client = $this->getClient();
        $path = storage_path('app/backups/' . $backupFile);
        
        return $client->uploadFile($path, 'backups/' . $backupFile);
    }
PHP;
    }

    protected function getDownloadBackupMethod(): string
    {
        return <<<'PHP'
    public function downloadBackup($backupFile)
    {
        $client = $this->getClient();
        $path = storage_path('app/backups/' . $backupFile);
        
        return $client->downloadFile('backups/' . $backupFile, $path);
    }
PHP;
    }

    protected function getListCloudBackupsMethod(): string
    {
        return <<<'PHP'
    public function listCloudBackups()
    {
        $client = $this->getClient();
        return $client->listFiles('backups');
    }
PHP;
    }

    protected function getScheduleBackupMethod(): string
    {
        return <<<'PHP'
    public function scheduleBackup($schedule)
    {
        $command = sprintf(
            'php artisan backup:run --schedule=%s',
            $schedule
        );
        
        exec($command);
        
        return true;
    }
PHP;
    }

    protected function getScheduleMethod(): string
    {
        return <<<'PHP'
    public function getSchedule()
    {
        return config('database-backup.schedule');
    }
PHP;
    }

    protected function getUpdateScheduleMethod(): string
    {
        return <<<'PHP'
    public function updateSchedule($schedule)
    {
        config(['database-backup.schedule' => $schedule]);
        
        return true;
    }
PHP;
    }

    protected function getDeleteScheduleMethod(): string
    {
        return <<<'PHP'
    public function deleteSchedule()
    {
        config(['database-backup.schedule' => null]);
        
        return true;
    }
PHP;
    }

    protected function getNotifyBackupSuccessMethod(): string
    {
        return <<<'PHP'
    public function notifyBackupSuccess($backupFile)
    {
        $notification = new BackupSuccessNotification($backupFile);
        $notification->notify();
        
        return true;
    }
PHP;
    }

    protected function getNotifyBackupFailureMethod(): string
    {
        return <<<'PHP'
    public function notifyBackupFailure($error)
    {
        $notification = new BackupFailureNotification($error);
        $notification->notify();
        
        return true;
    }
PHP;
    }

    protected function getNotifyStorageFullMethod(): string
    {
        return <<<'PHP'
    public function notifyStorageFull()
    {
        $notification = new StorageFullNotification();
        $notification->notify();
        
        return true;
    }
PHP;
    }
} 