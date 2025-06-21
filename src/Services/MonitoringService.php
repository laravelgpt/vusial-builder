<?php

namespace LaravelBuilder\VisualBuilder\Services;

class MonitoringService
{
    protected array $config;
    protected ?TelegramBotService $telegramService;
    protected array $metrics;

    public function __construct()
    {
        $this->config = [
            'system_monitoring' => true,
            'performance_monitoring' => true,
            'error_monitoring' => true,
            'uptime_monitoring' => true,
            'database_monitoring' => true,
            'notify_on_issues' => true,
            'alert_thresholds' => [
                'cpu_usage' => 80,
                'memory_usage' => 85,
                'disk_usage' => 90,
                'error_rate' => 5,
                'response_time' => 2000
            ]
        ];
        $this->telegramService = null;
        $this->metrics = [];
    }

    /**
     * Set configuration
     */
    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Set Telegram service for notifications
     */
    public function setTelegramService(TelegramBotService $telegramService): void
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Collect all system metrics
     */
    public function collectMetrics(): array
    {
        $metrics = [
            'timestamp' => date('Y-m-d H:i:s'),
            'system' => $this->getSystemMetrics(),
            'performance' => $this->getPerformanceMetrics(),
            'errors' => $this->getErrorMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'uptime' => $this->getUptimeMetrics()
        ];

        $this->metrics = $metrics;
        $this->checkAlerts($metrics);

        return $metrics;
    }

    /**
     * Get system metrics
     */
    protected function getSystemMetrics(): array
    {
        $metrics = [
            'cpu_usage' => 0,
            'memory_usage' => 0,
            'disk_usage' => 0,
            'load_average' => [],
            'processes' => 0,
            'uptime' => 0
        ];

        // CPU Usage
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $metrics['load_average'] = $load;
            
            // Estimate CPU usage from load average
            $cpuCores = $this->getCpuCores();
            if ($cpuCores > 0) {
                $metrics['cpu_usage'] = min(100, ($load[0] / $cpuCores) * 100);
            }
        }

        // Memory Usage
        $memoryInfo = $this->getMemoryInfo();
        if ($memoryInfo) {
            $metrics['memory_usage'] = round(($memoryInfo['used'] / $memoryInfo['total']) * 100, 2);
        }

        // Disk Usage
        $diskInfo = $this->getDiskInfo();
        if ($diskInfo) {
            $metrics['disk_usage'] = round(($diskInfo['used'] / $diskInfo['total']) * 100, 2);
        }

        // Process Count
        $metrics['processes'] = $this->getProcessCount();

        // System Uptime
        $metrics['uptime'] = $this->getSystemUptime();

        return $metrics;
    }

    /**
     * Get performance metrics
     */
    protected function getPerformanceMetrics(): array
    {
        $metrics = [
            'response_time' => 0,
            'requests_per_second' => 0,
            'memory_peak' => 0,
            'php_version' => PHP_VERSION,
            'laravel_version' => 'Unknown'
        ];

        // Memory peak usage
        $metrics['memory_peak'] = memory_get_peak_usage(true);

        // Try to get Laravel version
        $composerJsonPath = dirname(__DIR__, 4) . '/composer.json';
        if (file_exists($composerJsonPath)) {
            $composerData = json_decode(file_get_contents($composerJsonPath), true);
            if ($composerData && isset($composerData['require']['laravel/framework'])) {
                $metrics['laravel_version'] = $composerData['require']['laravel/framework'];
            }
        }

        // Response time (simulated for now)
        $startTime = microtime(true);
        // Simulate a small operation
        $metrics['response_time'] = round((microtime(true) - $startTime) * 1000, 2);

        return $metrics;
    }

    /**
     * Get error metrics
     */
    protected function getErrorMetrics(): array
    {
        $metrics = [
            'error_count' => 0,
            'error_rate' => 0,
            'recent_errors' => [],
            'log_size' => 0
        ];

        $logPath = dirname(__DIR__, 4) . '/storage/logs';
        if (is_dir($logPath)) {
            $logFiles = glob($logPath . '/*.log');
            $totalErrors = 0;
            $recentErrors = [];

            foreach ($logFiles as $logFile) {
                $logSize = filesize($logFile);
                $metrics['log_size'] += $logSize;

                // Count errors in recent lines
                $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($lines) {
                    $recentLines = array_slice($lines, -1000);
                    foreach ($recentLines as $line) {
                        if (stripos($line, 'ERROR') !== false || stripos($line, 'Exception') !== false) {
                            $totalErrors++;
                            if (count($recentErrors) < 10) {
                                $recentErrors[] = [
                                    'message' => substr($line, 0, 200),
                                    'file' => basename($logFile),
                                    'timestamp' => date('Y-m-d H:i:s', filemtime($logFile))
                                ];
                            }
                        }
                    }
                }
            }

            $metrics['error_count'] = $totalErrors;
            $metrics['recent_errors'] = $recentErrors;
        }

        return $metrics;
    }

    /**
     * Get database metrics
     */
    protected function getDatabaseMetrics(): array
    {
        $metrics = [
            'connection_status' => 'unknown',
            'query_count' => 0,
            'slow_queries' => 0,
            'database_size' => 0,
            'table_count' => 0
        ];

        // For now, we'll return basic metrics
        // In a real implementation, you'd connect to the database and get actual metrics
        $metrics['connection_status'] = 'connected'; // Assume connected for now

        return $metrics;
    }

    /**
     * Get uptime metrics
     */
    protected function getUptimeMetrics(): array
    {
        $metrics = [
            'system_uptime' => 0,
            'application_uptime' => 0,
            'last_restart' => 'Unknown',
            'uptime_percentage' => 100
        ];

        // System uptime
        $uptime = $this->getSystemUptime();
        $metrics['system_uptime'] = $uptime;

        // Application uptime (simulated)
        $metrics['application_uptime'] = $uptime;

        return $metrics;
    }

    /**
     * Get CPU cores count
     */
    protected function getCpuCores(): int
    {
        if (is_file('/proc/cpuinfo')) {
            $cpuInfo = file_get_contents('/proc/cpuinfo');
            return substr_count($cpuInfo, 'processor');
        }

        if (function_exists('sysconf')) {
            return sysconf('_SC_NPROCESSORS_ONLN');
        }

        return 1; // Default fallback
    }

    /**
     * Get memory information
     */
    protected function getMemoryInfo(): ?array
    {
        if (is_file('/proc/meminfo')) {
            $memInfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)/', $memInfo, $total);
            preg_match('/MemAvailable:\s+(\d+)/', $memInfo, $available);

            if (isset($total[1]) && isset($available[1])) {
                $totalKb = (int) $total[1];
                $availableKb = (int) $available[1];
                $usedKb = $totalKb - $availableKb;

                return [
                    'total' => $totalKb * 1024,
                    'used' => $usedKb * 1024,
                    'available' => $availableKb * 1024
                ];
            }
        }

        return null;
    }

    /**
     * Get disk information
     */
    protected function getDiskInfo(): ?array
    {
        $path = dirname(__DIR__, 4);
        $total = disk_total_space($path);
        $free = disk_free_space($path);

        if ($total && $free) {
            return [
                'total' => $total,
                'used' => $total - $free,
                'free' => $free
            ];
        }

        return null;
    }

    /**
     * Get process count
     */
    protected function getProcessCount(): int
    {
        if (is_file('/proc/stat')) {
            $stat = file_get_contents('/proc/stat');
            preg_match('/processes\s+(\d+)/', $stat, $matches);
            return isset($matches[1]) ? (int) $matches[1] : 0;
        }

        return 0;
    }

    /**
     * Get system uptime
     */
    protected function getSystemUptime(): int
    {
        if (is_file('/proc/uptime')) {
            $uptime = file_get_contents('/proc/uptime');
            $parts = explode(' ', $uptime);
            return (int) $parts[0];
        }

        return time() - ($_SERVER['REQUEST_TIME'] ?? time());
    }

    /**
     * Check for alerts based on thresholds
     */
    protected function checkAlerts(array $metrics): void
    {
        $alerts = [];

        // CPU Usage Alert
        if ($metrics['system']['cpu_usage'] > $this->config['alert_thresholds']['cpu_usage']) {
            $alerts[] = [
                'type' => 'cpu_usage',
                'severity' => 'warning',
                'message' => "High CPU usage: {$metrics['system']['cpu_usage']}%",
                'threshold' => $this->config['alert_thresholds']['cpu_usage']
            ];
        }

        // Memory Usage Alert
        if ($metrics['system']['memory_usage'] > $this->config['alert_thresholds']['memory_usage']) {
            $alerts[] = [
                'type' => 'memory_usage',
                'severity' => 'warning',
                'message' => "High memory usage: {$metrics['system']['memory_usage']}%",
                'threshold' => $this->config['alert_thresholds']['memory_usage']
            ];
        }

        // Disk Usage Alert
        if ($metrics['system']['disk_usage'] > $this->config['alert_thresholds']['disk_usage']) {
            $alerts[] = [
                'type' => 'disk_usage',
                'severity' => 'critical',
                'message' => "High disk usage: {$metrics['system']['disk_usage']}%",
                'threshold' => $this->config['alert_thresholds']['disk_usage']
            ];
        }

        // Error Rate Alert
        if ($metrics['errors']['error_count'] > $this->config['alert_thresholds']['error_rate']) {
            $alerts[] = [
                'type' => 'error_rate',
                'severity' => 'error',
                'message' => "High error count: {$metrics['errors']['error_count']} errors",
                'threshold' => $this->config['alert_thresholds']['error_rate']
            ];
        }

        // Send alerts if configured
        if ($this->config['notify_on_issues'] && !empty($alerts) && $this->telegramService) {
            $this->sendMonitoringAlerts($alerts, $metrics);
        }
    }

    /**
     * Send monitoring alerts
     */
    protected function sendMonitoringAlerts(array $alerts, array $metrics): void
    {
        if (!$this->telegramService) {
            return;
        }

        $message = "📊 <b>System Monitoring Alert</b>\n\n";
        $message .= "Time: " . $metrics['timestamp'] . "\n\n";

        foreach ($alerts as $alert) {
            $icon = match($alert['severity']) {
                'critical' => '🚨',
                'error' => '❌',
                'warning' => '⚠️',
                default => 'ℹ️'
            };

            $message .= "{$icon} <b>{$alert['type']}</b>\n";
            $message .= "{$alert['message']}\n";
            $message .= "Threshold: {$alert['threshold']}\n\n";
        }

        $message .= "📈 <b>Current Metrics:</b>\n";
        $message .= "CPU: {$metrics['system']['cpu_usage']}%\n";
        $message .= "Memory: {$metrics['system']['memory_usage']}%\n";
        $message .= "Disk: {$metrics['system']['disk_usage']}%\n";
        $message .= "Errors: {$metrics['errors']['error_count']}\n";

        $severity = 'warning';
        foreach ($alerts as $alert) {
            if ($alert['severity'] === 'critical') {
                $severity = 'error';
                break;
            }
        }

        $this->telegramService->sendSystemAlert($message, $severity);
    }

    /**
     * Get monitoring dashboard data
     */
    public function getDashboardData(): array
    {
        $metrics = $this->collectMetrics();
        
        return [
            'current_metrics' => $metrics,
            'system_health' => $this->calculateSystemHealth($metrics),
            'alerts' => $this->getActiveAlerts($metrics),
            'trends' => $this->getTrends(),
            'recommendations' => $this->generateRecommendations($metrics)
        ];
    }

    /**
     * Calculate system health score
     */
    protected function calculateSystemHealth(array $metrics): int
    {
        $score = 100;

        // Deduct points for high resource usage
        if ($metrics['system']['cpu_usage'] > 80) {
            $score -= 20;
        } elseif ($metrics['system']['cpu_usage'] > 60) {
            $score -= 10;
        }

        if ($metrics['system']['memory_usage'] > 85) {
            $score -= 20;
        } elseif ($metrics['system']['memory_usage'] > 70) {
            $score -= 10;
        }

        if ($metrics['system']['disk_usage'] > 90) {
            $score -= 30;
        } elseif ($metrics['system']['disk_usage'] > 80) {
            $score -= 15;
        }

        // Deduct points for errors
        if ($metrics['errors']['error_count'] > 10) {
            $score -= 20;
        } elseif ($metrics['errors']['error_count'] > 5) {
            $score -= 10;
        }

        return max(0, $score);
    }

    /**
     * Get active alerts
     */
    protected function getActiveAlerts(array $metrics): array
    {
        $alerts = [];

        if ($metrics['system']['cpu_usage'] > $this->config['alert_thresholds']['cpu_usage']) {
            $alerts[] = 'High CPU usage';
        }

        if ($metrics['system']['memory_usage'] > $this->config['alert_thresholds']['memory_usage']) {
            $alerts[] = 'High memory usage';
        }

        if ($metrics['system']['disk_usage'] > $this->config['alert_thresholds']['disk_usage']) {
            $alerts[] = 'High disk usage';
        }

        if ($metrics['errors']['error_count'] > $this->config['alert_thresholds']['error_rate']) {
            $alerts[] = 'High error rate';
        }

        return $alerts;
    }

    /**
     * Get trends (simplified for now)
     */
    protected function getTrends(): array
    {
        return [
            'cpu_trend' => 'stable',
            'memory_trend' => 'stable',
            'disk_trend' => 'stable',
            'error_trend' => 'stable'
        ];
    }

    /**
     * Generate recommendations
     */
    protected function generateRecommendations(array $metrics): array
    {
        $recommendations = [];

        if ($metrics['system']['cpu_usage'] > 80) {
            $recommendations[] = 'Consider optimizing CPU-intensive operations';
            $recommendations[] = 'Check for runaway processes';
        }

        if ($metrics['system']['memory_usage'] > 85) {
            $recommendations[] = 'Optimize memory usage in application';
            $recommendations[] = 'Consider increasing server memory';
        }

        if ($metrics['system']['disk_usage'] > 90) {
            $recommendations[] = 'Clean up unnecessary files';
            $recommendations[] = 'Consider expanding disk space';
        }

        if ($metrics['errors']['error_count'] > 10) {
            $recommendations[] = 'Review and fix application errors';
            $recommendations[] = 'Check log files for patterns';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'System is running optimally';
        }

        return $recommendations;
    }

    /**
     * Save metrics to storage
     */
    public function saveMetrics(array $metrics): void
    {
        $metricsFile = dirname(__DIR__, 4) . '/storage/app/monitoring_metrics.json';
        $metricsDir = dirname($metricsFile);
        
        if (!is_dir($metricsDir)) {
            mkdir($metricsDir, 0755, true);
        }

        file_put_contents($metricsFile, json_encode($metrics, JSON_PRETTY_PRINT));
    }

    /**
     * Get historical metrics
     */
    public function getHistoricalMetrics(int $hours = 24): array
    {
        $metricsFile = dirname(__DIR__, 4) . '/storage/app/monitoring_metrics.json';
        
        if (file_exists($metricsFile)) {
            $metrics = json_decode(file_get_contents($metricsFile), true);
            return $metrics ?: [];
        }

        return [];
    }
} 