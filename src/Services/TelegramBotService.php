<?php

namespace LaravelBuilder\VisualBuilder\Services;

class TelegramBotService
{
    protected string $token;
    protected string $webhookUrl;
    protected array $allowedUsers;
    protected bool $backupNotifications;
    protected bool $systemAlerts;

    public function __construct()
    {
        // Default configuration - can be overridden via setConfig method
        $this->token = '';
        $this->webhookUrl = '';
        $this->allowedUsers = [];
        $this->backupNotifications = true;
        $this->systemAlerts = true;
    }

    /**
     * Set configuration
     */
    public function setConfig(array $config): void
    {
        $this->token = $config['token'] ?? '';
        $this->webhookUrl = $config['webhook_url'] ?? '';
        $this->allowedUsers = $config['allowed_users'] ?? [];
        $this->backupNotifications = $config['backup_notifications'] ?? true;
        $this->systemAlerts = $config['system_alerts'] ?? true;
    }

    /**
     * Set webhook for the bot
     */
    public function setWebhook(): bool
    {
        if (empty($this->token) || empty($this->webhookUrl)) {
            return false;
        }

        try {
            $data = [
                'url' => $this->webhookUrl,
                'allowed_updates' => ['message', 'callback_query'],
                'drop_pending_updates' => true
            ];

            $response = $this->makeRequest("https://api.telegram.org/bot{$this->token}/setWebhook", 'POST', $data);
            return isset($response['ok']) && $response['ok'];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove webhook
     */
    public function removeWebhook(): bool
    {
        if (empty($this->token)) {
            return false;
        }

        try {
            $response = $this->makeRequest("https://api.telegram.org/bot{$this->token}/deleteWebhook", 'POST');
            return isset($response['ok']) && $response['ok'];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send message to a specific chat
     */
    public function sendMessage(int $chatId, string $message, array $options = []): bool
    {
        if (!$this->isUserAllowed($chatId)) {
            return false;
        }

        try {
            $data = array_merge([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ], $options);

            $response = $this->makeRequest("https://api.telegram.org/bot{$this->token}/sendMessage", 'POST', $data);
            return isset($response['ok']) && $response['ok'];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send message to all allowed users
     */
    public function broadcastMessage(string $message, array $options = []): array
    {
        $results = [];
        
        foreach ($this->allowedUsers as $userId) {
            $results[$userId] = $this->sendMessage((int) $userId, $message, $options);
        }

        return $results;
    }

    /**
     * Send backup notification
     */
    public function sendBackupNotification(string $status, string $details = ''): array
    {
        if (!$this->backupNotifications) {
            return [];
        }

        $message = "🔄 <b>Backup Status</b>\n\n";
        $message .= "Status: {$status}\n";
        
        if ($details) {
            $message .= "Details: {$details}\n";
        }

        $message .= "\nTime: " . date('Y-m-d H:i:s');

        return $this->broadcastMessage($message);
    }

    /**
     * Send system alert
     */
    public function sendSystemAlert(string $alert, string $level = 'info'): array
    {
        if (!$this->systemAlerts) {
            return [];
        }

        $icons = [
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'error' => '❌',
            'success' => '✅'
        ];

        $icon = $icons[$level] ?? 'ℹ️';

        $message = "{$icon} <b>System Alert</b>\n\n";
        $message .= "Alert: {$alert}\n";
        $message .= "Level: {$level}\n";
        $message .= "Time: " . date('Y-m-d H:i:s');

        return $this->broadcastMessage($message);
    }

    /**
     * Get bot information
     */
    public function getBotInfo(): ?array
    {
        if (empty($this->token)) {
            return null;
        }

        try {
            $response = $this->makeRequest("https://api.telegram.org/bot{$this->token}/getMe", 'GET');
            return $response['result'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo(): ?array
    {
        if (empty($this->token)) {
            return null;
        }

        try {
            $response = $this->makeRequest("https://api.telegram.org/bot{$this->token}/getWebhookInfo", 'GET');
            return $response['result'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Handle incoming webhook
     */
    public function handleWebhook(array $update): void
    {
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }

        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
    }

    /**
     * Handle incoming message
     */
    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $from = $message['from'];

        if (!$this->isUserAllowed($chatId)) {
            $this->sendMessage($chatId, '❌ You are not authorized to use this bot.');
            return;
        }

        // Handle commands
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $text, $from);
            return;
        }

        // Handle regular messages
        $this->handleRegularMessage($chatId, $text, $from);
    }

    /**
     * Handle command
     */
    protected function handleCommand(int $chatId, string $text, array $from): void
    {
        $command = strtolower(trim($text));

        switch ($command) {
            case '/start':
                $this->sendMessage($chatId, $this->getWelcomeMessage());
                break;
            case '/help':
                $this->sendMessage($chatId, $this->getHelpMessage());
                break;
            case '/status':
                $this->sendMessage($chatId, $this->getSystemStatus());
                break;
            case '/backup':
                $this->handleBackupCommand($chatId);
                break;
            default:
                $this->sendMessage($chatId, '❓ Unknown command. Use /help for available commands.');
        }
    }

    /**
     * Handle regular message
     */
    protected function handleRegularMessage(int $chatId, string $text, array $from): void
    {
        // Echo back the message for now
        $this->sendMessage($chatId, "📝 You said: {$text}");
    }

    /**
     * Handle callback query
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        if (!$this->isUserAllowed($chatId)) {
            return;
        }

        // Handle different callback data
        switch ($data) {
            case 'backup_now':
                $this->handleBackupCommand($chatId);
                break;
            case 'system_status':
                $this->sendMessage($chatId, $this->getSystemStatus());
                break;
            default:
                $this->sendMessage($chatId, '❓ Unknown callback data.');
        }
    }

    /**
     * Handle backup command
     */
    protected function handleBackupCommand(int $chatId): void
    {
        $this->sendMessage($chatId, '🔄 Initiating backup...');
        
        // Here you would trigger the actual backup process
        // For now, we'll just send a mock response
        $this->sendMessage($chatId, '✅ Backup completed successfully!');
    }

    /**
     * Get welcome message
     */
    protected function getWelcomeMessage(): string
    {
        return "🤖 <b>Welcome to Laravel Visual Builder Bot!</b>\n\n" .
               "I'm here to help you manage your Laravel application.\n\n" .
               "Use /help to see available commands.";
    }

    /**
     * Get help message
     */
    protected function getHelpMessage(): string
    {
        return "📚 <b>Available Commands:</b>\n\n" .
               "/start - Start the bot\n" .
               "/help - Show this help message\n" .
               "/status - Get system status\n" .
               "/backup - Trigger backup\n\n" .
               "You can also use inline buttons for quick actions.";
    }

    /**
     * Get system status
     */
    protected function getSystemStatus(): string
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $diskUsed = $diskTotal - $diskFree;
        $diskUsagePercent = round(($diskUsed / $diskTotal) * 100, 2);

        return "📊 <b>System Status</b>\n\n" .
               "Memory Usage: " . $this->formatBytes($memoryUsage) . "\n" .
               "Memory Peak: " . $this->formatBytes($memoryPeak) . "\n" .
               "Disk Usage: {$diskUsagePercent}%\n" .
               "Uptime: " . $this->getUptime() . "\n" .
               "PHP Version: " . PHP_VERSION;
    }

    /**
     * Check if user is allowed
     */
    protected function isUserAllowed(int $chatId): bool
    {
        return in_array($chatId, $this->allowedUsers);
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

    /**
     * Get system uptime
     */
    protected function getUptime(): string
    {
        $uptime = time() - ($_SERVER['REQUEST_TIME'] ?? time());
        
        $days = floor($uptime / 86400);
        $hours = floor(($uptime % 86400) / 3600);
        $minutes = floor(($uptime % 3600) / 60);
        
        return "{$days}d {$hours}h {$minutes}m";
    }

    /**
     * Make HTTP request
     */
    protected function makeRequest(string $url, string $method = 'GET', array $data = []): array
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $httpCode !== 200) {
            throw new \Exception('HTTP request failed');
        }
        
        return json_decode($response, true) ?? [];
    }
} 