<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use LaravelBuilder\VisualBuilder\Services\MonitoringService;
use LaravelBuilder\VisualBuilder\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class MonitoringController extends Controller
{
    protected MonitoringService $monitoringService;
    protected ?TelegramBotService $telegramService;

    public function __construct(MonitoringService $monitoringService, ?TelegramBotService $telegramService = null)
    {
        $this->monitoringService = $monitoringService;
        $this->telegramService = $telegramService;
        
        if ($this->telegramService) {
            $this->monitoringService->setTelegramService($this->telegramService);
        }
    }

    /**
     * Get current metrics
     */
    public function metrics(): JsonResponse
    {
        $metrics = $this->monitoringService->collectMetrics();
        $this->monitoringService->saveMetrics($metrics);

        return response()->json([
            'success' => true,
            'data' => $metrics
        ]);
    }

    /**
     * Get monitoring dashboard data
     */
    public function dashboard(): JsonResponse
    {
        $dashboardData = $this->monitoringService->getDashboardData();

        return response()->json([
            'success' => true,
            'data' => $dashboardData
        ]);
    }

    /**
     * Get system metrics
     */
    public function system(): JsonResponse
    {
        $metrics = $this->monitoringService->collectMetrics();

        return response()->json([
            'success' => true,
            'data' => [
                'system' => $metrics['system'],
                'performance' => $metrics['performance'],
                'timestamp' => $metrics['timestamp']
            ]
        ]);
    }

    /**
     * Get error metrics
     */
    public function errors(): JsonResponse
    {
        $metrics = $this->monitoringService->collectMetrics();

        return response()->json([
            'success' => true,
            'data' => [
                'errors' => $metrics['errors'],
                'timestamp' => $metrics['timestamp']
            ]
        ]);
    }

    /**
     * Get database metrics
     */
    public function database(): JsonResponse
    {
        $metrics = $this->monitoringService->collectMetrics();

        return response()->json([
            'success' => true,
            'data' => [
                'database' => $metrics['database'],
                'timestamp' => $metrics['timestamp']
            ]
        ]);
    }

    /**
     * Get uptime metrics
     */
    public function uptime(): JsonResponse
    {
        $metrics = $this->monitoringService->collectMetrics();

        return response()->json([
            'success' => true,
            'data' => [
                'uptime' => $metrics['uptime'],
                'timestamp' => $metrics['timestamp']
            ]
        ]);
    }

    /**
     * Configure monitoring settings
     */
    public function configure(Request $request): JsonResponse
    {
        $config = $request->validate([
            'system_monitoring' => 'boolean',
            'performance_monitoring' => 'boolean',
            'error_monitoring' => 'boolean',
            'uptime_monitoring' => 'boolean',
            'database_monitoring' => 'boolean',
            'notify_on_issues' => 'boolean',
            'alert_thresholds' => 'array',
            'alert_thresholds.cpu_usage' => 'integer|min:0|max:100',
            'alert_thresholds.memory_usage' => 'integer|min:0|max:100',
            'alert_thresholds.disk_usage' => 'integer|min:0|max:100',
            'alert_thresholds.error_rate' => 'integer|min:0',
            'alert_thresholds.response_time' => 'integer|min:0'
        ]);

        $this->monitoringService->setConfig($config);

        return response()->json([
            'success' => true,
            'message' => 'Monitoring configuration updated successfully'
        ]);
    }

    /**
     * Get historical metrics
     */
    public function history(Request $request): JsonResponse
    {
        $hours = $request->get('hours', 24);
        $metrics = $this->monitoringService->getHistoricalMetrics($hours);

        return response()->json([
            'success' => true,
            'data' => $metrics,
            'hours' => $hours
        ]);
    }

    /**
     * Get monitoring alerts
     */
    public function alerts(): JsonResponse
    {
        $dashboardData = $this->monitoringService->getDashboardData();

        return response()->json([
            'success' => true,
            'data' => [
                'alerts' => $dashboardData['alerts'] ?? [],
                'system_health' => $dashboardData['system_health'] ?? 0,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Get monitoring recommendations
     */
    public function recommendations(): JsonResponse
    {
        $dashboardData = $this->monitoringService->getDashboardData();

        return response()->json([
            'success' => true,
            'data' => [
                'recommendations' => $dashboardData['recommendations'] ?? [],
                'system_health' => $dashboardData['system_health'] ?? 0
            ]
        ]);
    }

    /**
     * Test monitoring configuration
     */
    public function test(): JsonResponse
    {
        try {
            $testConfig = [
                'system_monitoring' => true,
                'performance_monitoring' => true,
                'error_monitoring' => true,
                'uptime_monitoring' => true,
                'database_monitoring' => true,
                'notify_on_issues' => false
            ];
            
            $this->monitoringService->setConfig($testConfig);
            $metrics = $this->monitoringService->collectMetrics();

            return response()->json([
                'success' => true,
                'message' => 'Monitoring test completed successfully',
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Monitoring test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monitoring statistics
     */
    public function stats(): JsonResponse
    {
        $metrics = $this->monitoringService->collectMetrics();
        $dashboardData = $this->monitoringService->getDashboardData();

        return response()->json([
            'success' => true,
            'data' => [
                'current_metrics' => $metrics,
                'system_health' => $dashboardData['system_health'] ?? 0,
                'alerts' => $dashboardData['alerts'] ?? [],
                'recommendations' => $dashboardData['recommendations'] ?? [],
                'trends' => $dashboardData['trends'] ?? []
            ]
        ]);
    }

    /**
     * Get real-time monitoring data
     */
    public function realtime(): JsonResponse
    {
        $metrics = $this->monitoringService->collectMetrics();

        return response()->json([
            'success' => true,
            'data' => [
                'cpu_usage' => $metrics['system']['cpu_usage'] ?? 0,
                'memory_usage' => $metrics['system']['memory_usage'] ?? 0,
                'disk_usage' => $metrics['system']['disk_usage'] ?? 0,
                'error_count' => $metrics['errors']['error_count'] ?? 0,
                'response_time' => $metrics['performance']['response_time'] ?? 0,
                'uptime' => $metrics['uptime']['system_uptime'] ?? 0,
                'timestamp' => $metrics['timestamp'] ?? date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Get monitoring trends
     */
    public function trends(): JsonResponse
    {
        $dashboardData = $this->monitoringService->getDashboardData();

        return response()->json([
            'success' => true,
            'data' => [
                'trends' => $dashboardData['trends'] ?? [],
                'current_metrics' => $dashboardData['current_metrics'] ?? []
            ]
        ]);
    }
} 