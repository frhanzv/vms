<?php

namespace App\Controllers;

class Config extends BaseController
{
    public function index()
    {
        $data = [
            'pageTitle' => 'System Configuration - SafeG',
            'logs' => $this->getSystemLogs()
        ];

        return view('config/index', $data);
    }

    private function getSystemLogs($limit = 100, $level = null)
    {
        $logPath = WRITEPATH . 'logs/';
        $logs = [];
        
        // Get all log files sorted by date (newest first)
        $logFiles = glob($logPath . 'log-*.log');
        rsort($logFiles);
        
        $count = 0;
        foreach ($logFiles as $file) {
            if ($count >= $limit) break;
            
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lines = array_reverse($lines); // Newest first
            
            foreach ($lines as $line) {
                if ($count >= $limit) break;
                
                // Parse log line: LEVEL - YYYY-MM-DD HH:MM:SS --> Message
                if (preg_match('/^(\w+)\s+-\s+(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\s+-->\s+(.+)$/', $line, $matches)) {
                    $logLevel = $matches[1];
                    $timestamp = $matches[2];
                    $message = $matches[3];
                    
                    // Filter by level if specified
                    if ($level && strtoupper($level) !== strtoupper($logLevel)) {
                        continue;
                    }
                    
                    $logs[] = [
                        'level' => $logLevel,
                        'timestamp' => $timestamp,
                        'message' => $message,
                        'color' => $this->getLogLevelColor($logLevel)
                    ];
                    
                    $count++;
                }
            }
        }
        
        return $logs;
    }
    
    private function getLogLevelColor($level)
    {
        $colors = [
            'ERROR' => 'red',
            'CRITICAL' => 'red',
            'ALERT' => 'red',
            'EMERGENCY' => 'red',
            'WARNING' => 'yellow',
            'NOTICE' => 'blue',
            'INFO' => 'green',
            'DEBUG' => 'blue'
        ];
        
        return $colors[strtoupper($level)] ?? 'gray';
    }
    
    public function getLogs()
    {
        $level = $this->request->getGet('level');
        $limit = $this->request->getGet('limit') ?? 100;
        
        $logs = $this->getSystemLogs($limit, $level);
        
        return $this->response->setJSON([
            'success' => true,
            'logs' => $logs
        ]);
    }
    
    public function exportLogs()
    {
        $level = $this->request->getGet('level');
        $logPath = WRITEPATH . 'logs/';
        
        // Get all log files
        $logFiles = glob($logPath . 'log-*.log');
        rsort($logFiles);
        
        $content = "System Logs Export - " . date('Y-m-d H:i:s') . "\n";
        $content .= str_repeat('=', 80) . "\n\n";
        
        foreach ($logFiles as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if ($level) {
                    if (stripos($line, $level) !== false) {
                        $content .= $line . "\n";
                    }
                } else {
                    $content .= $line . "\n";
                }
            }
        }
        
        return $this->response
            ->setHeader('Content-Type', 'text/plain')
            ->setHeader('Content-Disposition', 'attachment; filename="system-logs-' . date('Y-m-d-His') . '.txt"')
            ->setBody($content);
    }
}

