<?php

namespace LaravelBuilder\VisualBuilder\Services;

class CloudStorageService
{
    protected string $driver;
    protected array $config;
    protected $client;

    public function __construct()
    {
        $this->driver = 'local';
        $this->config = [];
        $this->client = null;
    }

    /**
     * Set configuration
     */
    public function setConfig(array $config): void
    {
        $this->driver = $config['driver'] ?? 'local';
        $this->config = $config;
        $this->initializeClient();
    }

    /**
     * Initialize storage client
     */
    protected function initializeClient(): void
    {
        switch ($this->driver) {
            case 's3':
                $this->initializeS3Client();
                break;
            case 'gcs':
                $this->initializeGCSClient();
                break;
            case 'azure':
                $this->initializeAzureClient();
                break;
            case 'dropbox':
                $this->initializeDropboxClient();
                break;
            default:
                $this->client = null; // Local storage
        }
    }

    /**
     * Initialize AWS S3 client
     */
    protected function initializeS3Client(): void
    {
        $this->client = [
            'type' => 's3',
            'bucket' => $this->config['bucket'] ?? '',
            'region' => $this->config['region'] ?? 'us-east-1',
            'key' => $this->config['key'] ?? '',
            'secret' => $this->config['secret'] ?? ''
        ];
    }

    /**
     * Initialize Google Cloud Storage client
     */
    protected function initializeGCSClient(): void
    {
        $this->client = [
            'type' => 'gcs',
            'bucket' => $this->config['bucket'] ?? '',
            'project_id' => $this->config['project_id'] ?? '',
            'key_file' => $this->config['key_file'] ?? ''
        ];
    }

    /**
     * Initialize Azure Blob Storage client
     */
    protected function initializeAzureClient(): void
    {
        $this->client = [
            'type' => 'azure',
            'container' => $this->config['container'] ?? '',
            'account' => $this->config['account'] ?? '',
            'key' => $this->config['key'] ?? ''
        ];
    }

    /**
     * Initialize Dropbox client
     */
    protected function initializeDropboxClient(): void
    {
        $this->client = [
            'type' => 'dropbox',
            'access_token' => $this->config['access_token'] ?? '',
            'app_key' => $this->config['app_key'] ?? '',
            'app_secret' => $this->config['app_secret'] ?? ''
        ];
    }

    /**
     * Upload file to cloud storage
     */
    public function upload(string $localPath, string $remotePath, array $options = []): array
    {
        if (!file_exists($localPath)) {
            return ['success' => false, 'error' => 'Local file not found'];
        }

        try {
            switch ($this->driver) {
                case 's3':
                    return $this->uploadToS3($localPath, $remotePath, $options);
                case 'gcs':
                    return $this->uploadToGCS($localPath, $remotePath, $options);
                case 'azure':
                    return $this->uploadToAzure($localPath, $remotePath, $options);
                case 'dropbox':
                    return $this->uploadToDropbox($localPath, $remotePath, $options);
                default:
                    return $this->uploadToLocal($localPath, $remotePath, $options);
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Upload to AWS S3
     */
    protected function uploadToS3(string $localPath, string $remotePath, array $options): array
    {
        $command = "aws s3 cp {$localPath} s3://{$this->client['bucket']}/{$remotePath}";
        
        if (isset($options['public']) && $options['public']) {
            $command .= " --acl public-read";
        }

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            return [
                'success' => true,
                'url' => "https://{$this->client['bucket']}.s3.{$this->client['region']}.amazonaws.com/{$remotePath}"
            ];
        }

        return ['success' => false, 'error' => implode("\n", $output)];
    }

    /**
     * Upload to Google Cloud Storage
     */
    protected function uploadToGCS(string $localPath, string $remotePath, array $options): array
    {
        $command = "gsutil cp {$localPath} gs://{$this->client['bucket']}/{$remotePath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            return [
                'success' => true,
                'url' => "https://storage.googleapis.com/{$this->client['bucket']}/{$remotePath}"
            ];
        }

        return ['success' => false, 'error' => implode("\n", $output)];
    }

    /**
     * Upload to Azure Blob Storage
     */
    protected function uploadToAzure(string $localPath, string $remotePath, array $options): array
    {
        $command = "az storage blob upload --account-name {$this->client['account']} --container-name {$this->client['container']} --name {$remotePath} --file {$localPath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            return [
                'success' => true,
                'url' => "https://{$this->client['account']}.blob.core.windows.net/{$this->client['container']}/{$remotePath}"
            ];
        }

        return ['success' => false, 'error' => implode("\n", $output)];
    }

    /**
     * Upload to Dropbox
     */
    protected function uploadToDropbox(string $localPath, string $remotePath, array $options): array
    {
        $command = "curl -X POST https://content.dropboxapi.com/2/files/upload --header 'Authorization: Bearer {$this->client['access_token']}' --header 'Dropbox-API-Arg: {\"path\": \"/{$remotePath}\", \"mode\": \"add\"}' --header 'Content-Type: application/octet-stream' --data-binary @{$localPath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            return [
                'success' => true,
                'url' => "https://www.dropbox.com/s/{$remotePath}"
            ];
        }

        return ['success' => false, 'error' => implode("\n", $output)];
    }

    /**
     * Upload to local storage
     */
    protected function uploadToLocal(string $localPath, string $remotePath, array $options): array
    {
        $targetPath = $this->config['path'] ?? dirname(__DIR__, 4) . '/storage/app/public/' . $remotePath;
        $targetDir = dirname($targetPath);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (copy($localPath, $targetPath)) {
            return [
                'success' => true,
                'url' => $targetPath
            ];
        }

        return ['success' => false, 'error' => 'Failed to copy file'];
    }

    /**
     * Download file from cloud storage
     */
    public function download(string $remotePath, string $localPath, array $options = []): array
    {
        try {
            switch ($this->driver) {
                case 's3':
                    return $this->downloadFromS3($remotePath, $localPath, $options);
                case 'gcs':
                    return $this->downloadFromGCS($remotePath, $localPath, $options);
                case 'azure':
                    return $this->downloadFromAzure($remotePath, $localPath, $options);
                case 'dropbox':
                    return $this->downloadFromDropbox($remotePath, $localPath, $options);
                default:
                    return $this->downloadFromLocal($remotePath, $localPath, $options);
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Download from AWS S3
     */
    protected function downloadFromS3(string $remotePath, string $localPath, array $options): array
    {
        $command = "aws s3 cp s3://{$this->client['bucket']}/{$remotePath} {$localPath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($localPath)) {
            return ['success' => true, 'size' => filesize($localPath)];
        }

        return ['success' => false, 'error' => implode("\n", $output)];
    }

    /**
     * Download from Google Cloud Storage
     */
    protected function downloadFromGCS(string $remotePath, string $localPath, array $options): array
    {
        $command = "gsutil cp gs://{$this->client['bucket']}/{$remotePath} {$localPath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($localPath)) {
            return ['success' => true, 'size' => filesize($localPath)];
        }

        return ['success' => false, 'error' => implode("\n", $output)];
    }

    /**
     * Download from Azure Blob Storage
     */
    protected function downloadFromAzure(string $remotePath, string $localPath, array $options): array
    {
        $command = "az storage blob download --account-name {$this->client['account']} --container-name {$this->client['container']} --name {$remotePath} --file {$localPath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($localPath)) {
            return ['success' => true, 'size' => filesize($localPath)];
        }

        return ['success' => false, 'error' => implode("\n", $output)];
    }

    /**
     * Download from Dropbox
     */
    protected function downloadFromDropbox(string $remotePath, string $localPath, array $options): array
    {
        $command = "curl -X POST https://content.dropboxapi.com/2/files/download --header 'Authorization: Bearer {$this->client['access_token']}' --header 'Dropbox-API-Arg: {\"path\": \"/{$remotePath}\"}' --output {$localPath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($localPath)) {
            return ['success' => true, 'size' => filesize($localPath)];
        }

        return ['success' => false, 'error' => implode("\n", $output)];
    }

    /**
     * Download from local storage
     */
    protected function downloadFromLocal(string $remotePath, string $localPath, array $options): array
    {
        $sourcePath = $this->config['path'] ?? dirname(__DIR__, 4) . '/storage/app/public/' . $remotePath;

        if (!file_exists($sourcePath)) {
            return ['success' => false, 'error' => 'Source file not found'];
        }

        $targetDir = dirname($localPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (copy($sourcePath, $localPath)) {
            return ['success' => true, 'size' => filesize($localPath)];
        }

        return ['success' => false, 'error' => 'Failed to copy file'];
    }

    /**
     * Delete file from cloud storage
     */
    public function delete(string $remotePath): array
    {
        try {
            switch ($this->driver) {
                case 's3':
                    return $this->deleteFromS3($remotePath);
                case 'gcs':
                    return $this->deleteFromGCS($remotePath);
                case 'azure':
                    return $this->deleteFromAzure($remotePath);
                case 'dropbox':
                    return $this->deleteFromDropbox($remotePath);
                default:
                    return $this->deleteFromLocal($remotePath);
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete from AWS S3
     */
    protected function deleteFromS3(string $remotePath): array
    {
        $command = "aws s3 rm s3://{$this->client['bucket']}/{$remotePath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        return ['success' => $returnCode === 0, 'error' => $returnCode !== 0 ? implode("\n", $output) : null];
    }

    /**
     * Delete from Google Cloud Storage
     */
    protected function deleteFromGCS(string $remotePath): array
    {
        $command = "gsutil rm gs://{$this->client['bucket']}/{$remotePath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        return ['success' => $returnCode === 0, 'error' => $returnCode !== 0 ? implode("\n", $output) : null];
    }

    /**
     * Delete from Azure Blob Storage
     */
    protected function deleteFromAzure(string $remotePath): array
    {
        $command = "az storage blob delete --account-name {$this->client['account']} --container-name {$this->client['container']} --name {$remotePath}";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        return ['success' => $returnCode === 0, 'error' => $returnCode !== 0 ? implode("\n", $output) : null];
    }

    /**
     * Delete from Dropbox
     */
    protected function deleteFromDropbox(string $remotePath): array
    {
        $command = "curl -X POST https://api.dropboxapi.com/2/files/delete_v2 --header 'Authorization: Bearer {$this->client['access_token']}' --header 'Content-Type: application/json' --data '{\"path\": \"/{$remotePath}\"}'";
        
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        return ['success' => $returnCode === 0, 'error' => $returnCode !== 0 ? implode("\n", $output) : null];
    }

    /**
     * Delete from local storage
     */
    protected function deleteFromLocal(string $remotePath): array
    {
        $filePath = $this->config['path'] ?? dirname(__DIR__, 4) . '/storage/app/public/' . $remotePath;

        if (file_exists($filePath) && unlink($filePath)) {
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'File not found or could not be deleted'];
    }

    /**
     * Get file URL
     */
    public function getUrl(string $remotePath): ?string
    {
        switch ($this->driver) {
            case 's3':
                return "https://{$this->client['bucket']}.s3.{$this->client['region']}.amazonaws.com/{$remotePath}";
            case 'gcs':
                return "https://storage.googleapis.com/{$this->client['bucket']}/{$remotePath}";
            case 'azure':
                return "https://{$this->client['account']}.blob.core.windows.net/{$this->client['container']}/{$remotePath}";
            case 'dropbox':
                return "https://www.dropbox.com/s/{$remotePath}";
            default:
                return $this->config['path'] ?? dirname(__DIR__, 4) . '/storage/app/public/' . $remotePath;
        }
    }

    /**
     * Check if file exists
     */
    public function exists(string $remotePath): bool
    {
        switch ($this->driver) {
            case 'local':
                $filePath = $this->config['path'] ?? dirname(__DIR__, 4) . '/storage/app/public/' . $remotePath;
                return file_exists($filePath);
            default:
                // For cloud storage, we'd need to implement a more complex check
                // For now, return false to be safe
                return false;
        }
    }

    /**
     * Get file size
     */
    public function getSize(string $remotePath): ?int
    {
        switch ($this->driver) {
            case 'local':
                $filePath = $this->config['path'] ?? dirname(__DIR__, 4) . '/storage/app/public/' . $remotePath;
                return file_exists($filePath) ? filesize($filePath) : null;
            default:
                // For cloud storage, we'd need to implement a more complex check
                return null;
        }
    }
} 