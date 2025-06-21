<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use LaravelBuilder\VisualBuilder\Services\SecurityService;
use LaravelBuilder\VisualBuilder\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SecurityController extends Controller
{
    protected SecurityService $securityService;
    protected ?TelegramBotService $telegramService;

    public function __construct(SecurityService $securityService, ?TelegramBotService $telegramService = null)
    {
        $this->securityService = $securityService;
        $this->telegramService = $telegramService;
        
        if ($this->telegramService) {
            $this->securityService->setTelegramService($this->telegramService);
        }
    }

    /**
     * Perform security scan
     */
    public function scan(Request $request): JsonResponse
    {
        $config = $request->validate([
            'malware_scanning' => 'boolean',
            'vulnerability_scanning' => 'boolean',
            'file_integrity_monitoring' => 'boolean',
            'log_monitoring' => 'boolean',
            'notify_on_threats' => 'boolean'
        ]);

        if (!empty($config)) {
            $this->securityService->setConfig($config);
        }

        $result = $this->securityService->performSecurityScan();
        $this->securityService->saveScanResults($result);

        return response()->json($result);
    }

    /**
     * Get security statistics
     */
    public function stats(): JsonResponse
    {
        $stats = $this->securityService->getSecurityStats();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get security dashboard data
     */
    public function dashboard(): JsonResponse
    {
        $scanResult = $this->securityService->performSecurityScan();
        $stats = $this->securityService->getSecurityStats();

        return response()->json([
            'success' => true,
            'data' => [
                'current_scan' => $scanResult,
                'historical_stats' => $stats,
                'system_health' => $this->calculateSystemHealth($scanResult),
                'recommendations' => $scanResult['recommendations'] ?? []
            ]
        ]);
    }

    /**
     * Configure security settings
     */
    public function configure(Request $request): JsonResponse
    {
        $config = $request->validate([
            'malware_scanning' => 'boolean',
            'vulnerability_scanning' => 'boolean',
            'file_integrity_monitoring' => 'boolean',
            'log_monitoring' => 'boolean',
            'rate_limiting' => 'boolean',
            'notify_on_threats' => 'boolean'
        ]);

        $this->securityService->setConfig($config);

        return response()->json([
            'success' => true,
            'message' => 'Security configuration updated successfully'
        ]);
    }

    /**
     * Get malware scan results
     */
    public function malwareScan(): JsonResponse
    {
        $config = ['malware_scanning' => true, 'vulnerability_scanning' => false, 'file_integrity_monitoring' => false, 'log_monitoring' => false];
        $this->securityService->setConfig($config);
        
        $result = $this->securityService->performSecurityScan();

        return response()->json([
            'success' => true,
            'data' => [
                'malware_detected' => $result['malware_detected'] ?? [],
                'scan_duration' => $result['scan_duration'] ?? 0,
                'timestamp' => $result['timestamp'] ?? date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Get vulnerability scan results
     */
    public function vulnerabilityScan(): JsonResponse
    {
        $config = ['malware_scanning' => false, 'vulnerability_scanning' => true, 'file_integrity_monitoring' => false, 'log_monitoring' => false];
        $this->securityService->setConfig($config);
        
        $result = $this->securityService->performSecurityScan();

        return response()->json([
            'success' => true,
            'data' => [
                'vulnerabilities' => $result['vulnerabilities'] ?? [],
                'scan_duration' => $result['scan_duration'] ?? 0,
                'timestamp' => $result['timestamp'] ?? date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Get file integrity check results
     */
    public function integrityCheck(): JsonResponse
    {
        $config = ['malware_scanning' => false, 'vulnerability_scanning' => false, 'file_integrity_monitoring' => true, 'log_monitoring' => false];
        $this->securityService->setConfig($config);
        
        $result = $this->securityService->performSecurityScan();

        return response()->json([
            'success' => true,
            'data' => [
                'integrity_issues' => $result['integrity_issues'] ?? [],
                'scan_duration' => $result['scan_duration'] ?? 0,
                'timestamp' => $result['timestamp'] ?? date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Get log monitoring results
     */
    public function logMonitoring(): JsonResponse
    {
        $config = ['malware_scanning' => false, 'vulnerability_scanning' => false, 'file_integrity_monitoring' => false, 'log_monitoring' => true];
        $this->securityService->setConfig($config);
        
        $result = $this->securityService->performSecurityScan();

        return response()->json([
            'success' => true,
            'data' => [
                'log_threats' => $result['log_threats'] ?? [],
                'scan_duration' => $result['scan_duration'] ?? 0,
                'timestamp' => $result['timestamp'] ?? date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Get security recommendations
     */
    public function recommendations(): JsonResponse
    {
        $result = $this->securityService->performSecurityScan();

        return response()->json([
            'success' => true,
            'data' => [
                'recommendations' => $result['recommendations'] ?? [],
                'overall_score' => $result['overall_score'] ?? 0,
                'threats_found' => $result['threats_found'] ?? 0
            ]
        ]);
    }

    /**
     * Test security configuration
     */
    public function test(): JsonResponse
    {
        try {
            $testConfig = [
                'malware_scanning' => true,
                'vulnerability_scanning' => true,
                'file_integrity_monitoring' => false,
                'log_monitoring' => false,
                'notify_on_threats' => false
            ];
            
            $this->securityService->setConfig($testConfig);
            $result = $this->securityService->performSecurityScan();

            return response()->json([
                'success' => true,
                'message' => 'Security test completed successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Security test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get security alerts
     */
    public function alerts(): JsonResponse
    {
        $result = $this->securityService->performSecurityScan();
        
        $alerts = [];
        
        if (count($result['malware_detected'] ?? []) > 0) {
            $alerts[] = [
                'type' => 'malware',
                'severity' => 'high',
                'message' => count($result['malware_detected']) . ' malware threats detected',
                'count' => count($result['malware_detected'])
            ];
        }
        
        if (count($result['vulnerabilities'] ?? []) > 0) {
            $alerts[] = [
                'type' => 'vulnerability',
                'severity' => 'medium',
                'message' => count($result['vulnerabilities']) . ' vulnerabilities found',
                'count' => count($result['vulnerabilities'])
            ];
        }
        
        if (count($result['integrity_issues'] ?? []) > 0) {
            $alerts[] = [
                'type' => 'integrity',
                'severity' => 'high',
                'message' => count($result['integrity_issues']) . ' file integrity issues detected',
                'count' => count($result['integrity_issues'])
            ];
        }
        
        if (count($result['log_threats'] ?? []) > 0) {
            $alerts[] = [
                'type' => 'log_threat',
                'severity' => 'medium',
                'message' => count($result['log_threats']) . ' suspicious log entries found',
                'count' => count($result['log_threats'])
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'alerts' => $alerts,
                'total_alerts' => count($alerts),
                'security_score' => $result['overall_score'] ?? 0
            ]
        ]);
    }

    /**
     * Calculate system health based on scan results
     */
    protected function calculateSystemHealth(array $scanResult): array
    {
        $score = $scanResult['overall_score'] ?? 0;
        $threats = $scanResult['threats_found'] ?? 0;
        
        $status = 'excellent';
        if ($score < 50) {
            $status = 'critical';
        } elseif ($score < 70) {
            $status = 'poor';
        } elseif ($score < 85) {
            $status = 'good';
        }
        
        return [
            'score' => $score,
            'status' => $status,
            'threats' => $threats,
            'last_scan' => $scanResult['timestamp'] ?? date('Y-m-d H:i:s')
        ];
    }
} 