<?php

namespace LaravelBuilder\VisualBuilder\Services;

class SecurityService
{
    protected array $config;
    protected ?TelegramBotService $telegramService;

    public function __construct()
    {
        $this->config = [
            'malware_scanning' => true,
            'vulnerability_scanning' => true,
            'file_integrity_monitoring' => true,
            'log_monitoring' => true,
            'rate_limiting' => true,
            'notify_on_threats' => true
        ];
        $this->telegramService = null;
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
     * Perform comprehensive security scan
     */
    public function performSecurityScan(): array
    {
        $results = [
            'timestamp' => date('Y-m-d H:i:s'),
            'overall_score' => 0,
            'threats_found' => 0,
            'vulnerabilities' => [],
            'malware_detected' => [],
            'integrity_issues' => [],
            'recommendations' => [],
            'scan_duration' => 0
        ];

        $startTime = microtime(true);

        // Malware scanning
        if ($this->config['malware_scanning']) {
            $malwareResults = $this->scanForMalware();
            $results['malware_detected'] = $malwareResults['threats'];
            $results['threats_found'] += count($malwareResults['threats']);
        }

        // Vulnerability scanning
        if ($this->config['vulnerability_scanning']) {
            $vulnResults = $this->scanForVulnerabilities();
            $results['vulnerabilities'] = $vulnResults['vulnerabilities'];
            $results['threats_found'] += count($vulnResults['vulnerabilities']);
        }

        // File integrity monitoring
        if ($this->config['file_integrity_monitoring']) {
            $integrityResults = $this->checkFileIntegrity();
            $results['integrity_issues'] = $integrityResults['issues'];
            $results['threats_found'] += count($integrityResults['issues']);
        }

        // Log monitoring
        if ($this->config['log_monitoring']) {
            $logResults = $this->monitorLogs();
            $results['log_threats'] = $logResults['threats'];
            $results['threats_found'] += count($logResults['threats']);
        }

        // Calculate overall security score
        $results['overall_score'] = $this->calculateSecurityScore($results);

        // Generate recommendations
        $results['recommendations'] = $this->generateRecommendations($results);

        $results['scan_duration'] = round(microtime(true) - $startTime, 2);

        // Send notification if threats found
        if ($this->config['notify_on_threats'] && $results['threats_found'] > 0 && $this->telegramService) {
            $this->sendSecurityAlert($results);
        }

        return $results;
    }

    /**
     * Scan for malware
     */
    protected function scanForMalware(): array
    {
        $threats = [];
        $scanPaths = [
            dirname(__DIR__, 4) . '/public',
            dirname(__DIR__, 4) . '/storage',
            dirname(__DIR__, 4) . '/app'
        ];

        foreach ($scanPaths as $path) {
            if (!is_dir($path)) continue;

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $threat = $this->analyzeFileForMalware($file->getPathname());
                    if ($threat) {
                        $threats[] = $threat;
                    }
                }
            }
        }

        return ['threats' => $threats];
    }

    /**
     * Analyze individual file for malware
     */
    protected function analyzeFileForMalware(string $filePath): ?array
    {
        $content = file_get_contents($filePath);
        if (!$content) return null;

        $suspiciousPatterns = [
            'eval\s*\(',
            'base64_decode\s*\(',
            'gzinflate\s*\(',
            'str_rot13\s*\(',
            'preg_replace.*\/e',
            'assert\s*\(',
            'system\s*\(',
            'shell_exec\s*\(',
            'exec\s*\(',
            'passthru\s*\(',
            'file_get_contents\s*\(\s*[\'"]https?:\/\/',
            'curl_exec\s*\(',
            'file_put_contents\s*\(\s*[\'"]https?:\/\/',
            'fwrite\s*\(\s*[\'"]https?:\/\/',
            'include\s*\(\s*[\'"]https?:\/\/',
            'require\s*\(\s*[\'"]https?:\/\/',
            'include_once\s*\(\s*[\'"]https?:\/\/',
            'require_once\s*\(\s*[\'"]https?:\/\/'
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $content)) {
                return [
                    'type' => 'malware',
                    'file' => $filePath,
                    'pattern' => $pattern,
                    'severity' => 'high',
                    'description' => 'Suspicious code pattern detected'
                ];
            }
        }

        // Check for encoded/obfuscated content
        if (preg_match('/[a-zA-Z0-9\/+]{50,}={0,2}/', $content)) {
            $decoded = base64_decode($content, true);
            if ($decoded && strlen($decoded) > 100) {
                return [
                    'type' => 'malware',
                    'file' => $filePath,
                    'pattern' => 'base64_encoded_content',
                    'severity' => 'medium',
                    'description' => 'Large base64 encoded content detected'
                ];
            }
        }

        return null;
    }

    /**
     * Scan for vulnerabilities
     */
    protected function scanForVulnerabilities(): array
    {
        $vulnerabilities = [];

        // Check PHP version
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '8.0.0', '<')) {
            $vulnerabilities[] = [
                'type' => 'vulnerability',
                'component' => 'PHP',
                'version' => $phpVersion,
                'severity' => 'high',
                'description' => 'PHP version is outdated and may have security vulnerabilities',
                'recommendation' => 'Upgrade to PHP 8.0 or higher'
            ];
        }

        // Check file permissions
        $permissionIssues = $this->checkFilePermissions();
        $vulnerabilities = array_merge($vulnerabilities, $permissionIssues);

        return ['vulnerabilities' => $vulnerabilities];
    }

    /**
     * Check file permissions
     */
    protected function checkFilePermissions(): array
    {
        $issues = [];
        $criticalPaths = [
            dirname(__DIR__, 4) . '/.env',
            dirname(__DIR__, 4) . '/storage',
            dirname(__DIR__, 4) . '/bootstrap/cache'
        ];

        foreach ($criticalPaths as $path) {
            if (file_exists($path)) {
                $perms = fileperms($path);
                $octalPerms = substr(sprintf('%o', $perms), -4);

                if (is_file($path)) {
                    if ($octalPerms !== '0600' && $octalPerms !== '0640') {
                        $issues[] = [
                            'type' => 'vulnerability',
                            'component' => 'File Permissions',
                            'file' => $path,
                            'current_perms' => $octalPerms,
                            'severity' => 'high',
                            'description' => 'File has overly permissive permissions',
                            'recommendation' => 'Set permissions to 600 or 640'
                        ];
                    }
                } else {
                    if ($octalPerms !== '0750' && $octalPerms !== '0755') {
                        $issues[] = [
                            'type' => 'vulnerability',
                            'component' => 'Directory Permissions',
                            'directory' => $path,
                            'current_perms' => $octalPerms,
                            'severity' => 'medium',
                            'description' => 'Directory has overly permissive permissions',
                            'recommendation' => 'Set permissions to 750 or 755'
                        ];
                    }
                }
            }
        }

        return $issues;
    }

    /**
     * Check file integrity
     */
    protected function checkFileIntegrity(): array
    {
        $issues = [];
        $integrityFile = dirname(__DIR__, 4) . '/storage/app/file_integrity.json';

        if (!file_exists($integrityFile)) {
            // Create initial integrity file
            $this->createIntegrityFile($integrityFile);
            return ['issues' => []];
        }

        $storedHashes = json_decode(file_get_contents($integrityFile), true);
        if (!$storedHashes) {
            return ['issues' => []];
        }

        $criticalFiles = [
            dirname(__DIR__, 4) . '/.env',
            dirname(__DIR__, 4) . '/composer.json',
            dirname(__DIR__, 4) . '/config/app.php',
            dirname(__DIR__, 4) . '/routes/web.php'
        ];

        foreach ($criticalFiles as $file) {
            if (file_exists($file)) {
                $currentHash = hash_file('sha256', $file);
                $storedHash = $storedHashes[basename($file)] ?? null;

                if ($storedHash && $currentHash !== $storedHash) {
                    $issues[] = [
                        'type' => 'integrity',
                        'file' => $file,
                        'stored_hash' => $storedHash,
                        'current_hash' => $currentHash,
                        'severity' => 'high',
                        'description' => 'File integrity check failed - file may have been modified',
                        'recommendation' => 'Investigate unauthorized changes'
                    ];
                }
            }
        }

        return ['issues' => $issues];
    }

    /**
     * Create integrity file
     */
    protected function createIntegrityFile(string $integrityFile): void
    {
        $criticalFiles = [
            dirname(__DIR__, 4) . '/.env',
            dirname(__DIR__, 4) . '/composer.json',
            dirname(__DIR__, 4) . '/config/app.php',
            dirname(__DIR__, 4) . '/routes/web.php'
        ];

        $hashes = [];
        foreach ($criticalFiles as $file) {
            if (file_exists($file)) {
                $hashes[basename($file)] = hash_file('sha256', $file);
            }
        }

        $integrityDir = dirname($integrityFile);
        if (!is_dir($integrityDir)) {
            mkdir($integrityDir, 0755, true);
        }

        file_put_contents($integrityFile, json_encode($hashes, JSON_PRETTY_PRINT));
    }

    /**
     * Monitor logs for security threats
     */
    protected function monitorLogs(): array
    {
        $threats = [];
        $logPath = dirname(__DIR__, 4) . '/storage/logs';
        
        if (!is_dir($logPath)) {
            return ['threats' => $threats];
        }

        $logFiles = glob($logPath . '/*.log');
        $suspiciousPatterns = [
            'SQL injection',
            'XSS attack',
            'CSRF token mismatch',
            'Unauthorized access',
            'Failed login attempts',
            'Suspicious IP',
            'Rate limit exceeded',
            'File upload attack',
            'Directory traversal',
            'Command injection'
        ];

        foreach ($logFiles as $logFile) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!$lines) continue;

            // Check last 1000 lines
            $recentLines = array_slice($lines, -1000);

            foreach ($recentLines as $lineNumber => $line) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (stripos($line, $pattern) !== false) {
                        $threats[] = [
                            'type' => 'log_threat',
                            'log_file' => basename($logFile),
                            'line_number' => count($lines) - 1000 + $lineNumber + 1,
                            'pattern' => $pattern,
                            'severity' => 'medium',
                            'description' => "Suspicious activity detected in logs: {$pattern}",
                            'log_line' => substr($line, 0, 200) . '...'
                        ];
                    }
                }
            }
        }

        return ['threats' => $threats];
    }

    /**
     * Calculate security score
     */
    protected function calculateSecurityScore(array $results): int
    {
        $baseScore = 100;
        $deductions = 0;

        // Deduct points for each threat
        $deductions += count($results['malware_detected']) * 20;
        $deductions += count($results['vulnerabilities']) * 15;
        $deductions += count($results['integrity_issues']) * 10;
        $deductions += count($results['log_threats'] ?? []) * 5;

        $score = max(0, $baseScore - $deductions);
        return min(100, $score);
    }

    /**
     * Generate security recommendations
     */
    protected function generateRecommendations(array $results): array
    {
        $recommendations = [];

        if (count($results['malware_detected']) > 0) {
            $recommendations[] = 'Immediately remove or quarantine detected malware files';
            $recommendations[] = 'Scan entire system with antivirus software';
            $recommendations[] = 'Review file upload security measures';
        }

        if (count($results['vulnerabilities']) > 0) {
            $recommendations[] = 'Update vulnerable packages to latest secure versions';
            $recommendations[] = 'Review and fix file permission issues';
            $recommendations[] = 'Consider implementing automated security updates';
        }

        if (count($results['integrity_issues']) > 0) {
            $recommendations[] = 'Investigate unauthorized file modifications';
            $recommendations[] = 'Restore files from secure backups if necessary';
            $recommendations[] = 'Implement file integrity monitoring';
        }

        if (count($results['log_threats'] ?? []) > 0) {
            $recommendations[] = 'Review and block suspicious IP addresses';
            $recommendations[] = 'Implement rate limiting for login attempts';
            $recommendations[] = 'Enable two-factor authentication';
        }

        if ($results['overall_score'] < 50) {
            $recommendations[] = 'Conduct comprehensive security audit';
            $recommendations[] = 'Consider hiring security consultant';
            $recommendations[] = 'Implement security monitoring tools';
        }

        return $recommendations;
    }

    /**
     * Send security alert
     */
    protected function sendSecurityAlert(array $results): void
    {
        if (!$this->telegramService) {
            return;
        }

        $message = "🚨 <b>Security Alert</b>\n\n";
        $message .= "Security Score: {$results['overall_score']}/100\n";
        $message .= "Threats Found: {$results['threats_found']}\n";
        $message .= "Scan Duration: {$results['scan_duration']}s\n\n";

        if ($results['threats_found'] > 0) {
            $message .= "⚠️ <b>Threats Detected:</b>\n";
            
            if (count($results['malware_detected']) > 0) {
                $message .= "• Malware: " . count($results['malware_detected']) . " files\n";
            }
            
            if (count($results['vulnerabilities']) > 0) {
                $message .= "• Vulnerabilities: " . count($results['vulnerabilities']) . " issues\n";
            }
            
            if (count($results['integrity_issues']) > 0) {
                $message .= "• Integrity: " . count($results['integrity_issues']) . " issues\n";
            }
            
            if (count($results['log_threats'] ?? []) > 0) {
                $message .= "• Log Threats: " . count($results['log_threats']) . " events\n";
            }
        }

        $this->telegramService->sendSystemAlert($message, $results['overall_score'] < 50 ? 'error' : 'warning');
    }

    /**
     * Get security statistics
     */
    public function getSecurityStats(): array
    {
        $lastScanFile = dirname(__DIR__, 4) . '/storage/app/security_scan.json';
        
        if (file_exists($lastScanFile)) {
            $lastScan = json_decode(file_get_contents($lastScanFile), true);
            return [
                'last_scan' => $lastScan['timestamp'] ?? 'Never',
                'last_score' => $lastScan['overall_score'] ?? 0,
                'total_threats' => $lastScan['threats_found'] ?? 0,
                'scan_duration' => $lastScan['scan_duration'] ?? 0
            ];
        }

        return [
            'last_scan' => 'Never',
            'last_score' => 0,
            'total_threats' => 0,
            'scan_duration' => 0
        ];
    }

    /**
     * Save scan results
     */
    public function saveScanResults(array $results): void
    {
        $scanFile = dirname(__DIR__, 4) . '/storage/app/security_scan.json';
        $scanDir = dirname($scanFile);
        
        if (!is_dir($scanDir)) {
            mkdir($scanDir, 0755, true);
        }

        file_put_contents($scanFile, json_encode($results, JSON_PRETTY_PRINT));
    }
} 