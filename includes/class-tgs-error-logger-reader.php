<?php

/**
 * TGS Error Logger Reader
 *
 * Đọc và filter logs từ các module
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGS_Error_Logger_Reader
{
    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // Private constructor
    }

    /**
     * Get all blogs
     */
    public function get_all_blogs()
    {
        global $wpdb;

        if (!is_multisite()) {
            return [
                [
                    'blog_id' => 1,
                    'blogname' => get_bloginfo('name'),
                    'tgs_site_code' => '',
                    'domain' => get_site_url(),
                ]
            ];
        }

        $blogs = $wpdb->get_results("
            SELECT blog_id, domain, path
            FROM {$wpdb->base_prefix}blogs
            WHERE deleted = 0 AND spam = 0
            ORDER BY blog_id ASC
        ");

        $result = [];
        foreach ($blogs as $blog) {
            switch_to_blog($blog->blog_id);

            $tgs_site_code = $wpdb->get_var($wpdb->prepare(
                "SELECT tgs_site_code FROM {$wpdb->base_prefix}blogs WHERE blog_id = %d",
                $blog->blog_id
            ));

            $result[] = [
                'blog_id' => $blog->blog_id,
                'blogname' => get_bloginfo('name'),
                'tgs_site_code' => $tgs_site_code ?: '',
                'domain' => $blog->domain . $blog->path,
            ];

            restore_current_blog();
        }

        return $result;
    }

    /**
     * Get log directory path
     */
    public function get_log_directory($blog_id, $module)
    {
        if (is_multisite() && $blog_id > 1) {
            switch_to_blog($blog_id);
        }

        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];

        if (is_multisite() && $blog_id > 1) {
            restore_current_blog();
        }

        return $base_dir . '/tgs_logs/' . $module;
    }

    /**
     * Get available log files for a module
     */
    public function get_available_log_files($blog_id, $module)
    {
        $log_dir = $this->get_log_directory($blog_id, $module);

        if (!is_dir($log_dir)) {
            return [];
        }

        $files = [];
        $handle = opendir($log_dir);

        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'jsonl') {
                    $filepath = $log_dir . '/' . $file;
                    $files[] = [
                        'filename' => $file,
                        'date' => str_replace('.jsonl', '', $file),
                        'size' => filesize($filepath),
                        'modified' => filemtime($filepath),
                    ];
                }
            }
            closedir($handle);
        }

        // Sort by date descending
        usort($files, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $files;
    }

    /**
     * Read logs from a specific module and date
     */
    public function read_logs($blog_id, $module, $date, $filters = [])
    {
        $log_dir = $this->get_log_directory($blog_id, $module);
        $log_file = $log_dir . '/' . $date . '.jsonl';

        if (!file_exists($log_file)) {
            return [];
        }

        $logs = [];
        $handle = fopen($log_file, 'r');

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                $log = json_decode($line, true);
                if ($log && $this->match_filters($log, $filters)) {
                    $logs[] = $log;
                }
            }

            fclose($handle);
        }

        // Sort by timestamp descending
        usort($logs, function($a, $b) {
            return $b['unix_timestamp'] - $a['unix_timestamp'];
        });

        return $logs;
    }

    /**
     * Read logs from multiple modules
     */
    public function read_logs_multi_module($blog_id, $modules, $date, $filters = [])
    {
        $all_logs = [];

        foreach ($modules as $module) {
            $logs = $this->read_logs($blog_id, $module, $date, $filters);
            $all_logs = array_merge($all_logs, $logs);
        }

        // Sort by timestamp descending
        usort($all_logs, function($a, $b) {
            return $b['unix_timestamp'] - $a['unix_timestamp'];
        });

        return $all_logs;
    }

    /**
     * Check if log matches filters
     */
    private function match_filters($log, $filters)
    {
        if (empty($filters)) {
            return true;
        }

        // Filter by level
        if (!empty($filters['level']) && $log['level'] != $filters['level']) {
            return false;
        }

        // Filter by minimum level
        if (!empty($filters['min_level'])) {
            $levels = TGS_Error_Logger::get_levels();
            $min_level_int = $levels[$filters['min_level']] ?? 0;
            if ($log['level_int'] < $min_level_int) {
                return false;
            }
        }

        // Filter by user
        if (!empty($filters['user_id']) && $log['user_id'] != $filters['user_id']) {
            return false;
        }

        // Filter by security issues only
        if (!empty($filters['security_only']) && empty($log['security_issue'])) {
            return false;
        }

        // Search in message, file, function
        if (!empty($filters['search'])) {
            $search = mb_strtolower($filters['search'], 'UTF-8');
            $searchable = mb_strtolower(
                $log['message'] . ' ' .
                $log['file'] . ' ' .
                $log['function'] . ' ' .
                $log['user_login'],
                'UTF-8'
            );

            if (strpos($searchable, $search) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get summary statistics
     */
    public function get_summary_stats($blog_id, $module, $date)
    {
        $logs = $this->read_logs($blog_id, $module, $date);

        $stats = [
            'total' => count($logs),
            'by_level' => [],
            'security_issues' => 0,
            'by_user' => [],
            'by_file' => [],
        ];

        foreach ($logs as $log) {
            // Count by level
            $level = $log['level'];
            if (!isset($stats['by_level'][$level])) {
                $stats['by_level'][$level] = 0;
            }
            $stats['by_level'][$level]++;

            // Count security issues
            if (!empty($log['security_issue'])) {
                $stats['security_issues']++;
            }

            // Count by user
            if (!isset($stats['by_user'][$log['user_id']])) {
                $stats['by_user'][$log['user_id']] = [
                    'user_login' => $log['user_login'],
                    'count' => 0,
                ];
            }
            $stats['by_user'][$log['user_id']]['count']++;

            // Count by file
            if (!empty($log['file'])) {
                $file_key = basename($log['file']);
                if (!isset($stats['by_file'][$file_key])) {
                    $stats['by_file'][$file_key] = 0;
                }
                $stats['by_file'][$file_key]++;
            }
        }

        // Sort by count
        uasort($stats['by_user'], function($a, $b) {
            return $b['count'] - $a['count'];
        });

        arsort($stats['by_file']);

        return $stats;
    }

    /**
     * Get recent errors across all modules
     */
    public function get_recent_errors($blog_id, $limit = 50, $min_level = 'warning')
    {
        $modules = array_keys(TGS_Error_Logger::get_modules());
        $date = current_time('Y-m-d');

        $all_logs = $this->read_logs_multi_module($blog_id, $modules, $date, [
            'min_level' => $min_level
        ]);

        return array_slice($all_logs, 0, $limit);
    }

    /**
     * Get security issues
     */
    public function get_security_issues($blog_id, $days = 7)
    {
        $all_issues = [];

        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $logs = $this->read_logs($blog_id, 'security', $date);
            $all_issues = array_merge($all_issues, $logs);
        }

        // Sort by timestamp descending
        usort($all_issues, function($a, $b) {
            return $b['unix_timestamp'] - $a['unix_timestamp'];
        });

        return $all_issues;
    }

    /**
     * Get error count by module for a date range
     */
    public function get_module_stats($blog_id, $date_from, $date_to)
    {
        $modules = array_keys(TGS_Error_Logger::get_modules());
        $stats = [];

        foreach ($modules as $module) {
            $stats[$module] = [
                'total' => 0,
                'critical' => 0,
                'error' => 0,
                'warning' => 0,
            ];

            $current_date = strtotime($date_from);
            $end_date = strtotime($date_to);

            while ($current_date <= $end_date) {
                $date = date('Y-m-d', $current_date);
                $logs = $this->read_logs($blog_id, $module, $date);

                $stats[$module]['total'] += count($logs);

                foreach ($logs as $log) {
                    $level = $log['level'];
                    if (isset($stats[$module][$level])) {
                        $stats[$module][$level]++;
                    }
                }

                $current_date = strtotime('+1 day', $current_date);
            }
        }

        return $stats;
    }

    /**
     * Get total log file size for a module
     */
    public function get_module_size($blog_id, $module)
    {
        $log_dir = $this->get_log_directory($blog_id, $module);

        if (!is_dir($log_dir)) {
            return 0;
        }

        $total_size = 0;
        $handle = opendir($log_dir);

        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'jsonl') {
                    $filepath = $log_dir . '/' . $file;
                    $total_size += filesize($filepath);
                }
            }
            closedir($handle);
        }

        return $total_size;
    }
}
