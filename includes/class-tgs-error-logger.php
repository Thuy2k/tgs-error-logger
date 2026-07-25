<?php

/**
 * TGS Error Logger - Core Class
 *
 * Hệ thống log lỗi thông minh theo module
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGS_Error_Logger
{
    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Supported modules
     */
    private static $modules = [
        'pos' => 'POS System',
        'shop' => 'Quản trị hệ thống (TGS Shop Management)',
        'selling_policy' => 'Chính sách bán hàng',
        'purchase' => 'Quản lý mua hàng',
        'sync' => 'Đồng bộ dữ liệu',
        'api' => 'REST API / AJAX',
        'security' => 'Bảo mật',
        'database' => 'Cơ sở dữ liệu',
        'payment' => 'Thanh toán',
        'auth' => 'Xác thực',
        'system' => 'Hệ thống chung',
    ];

    /**
     * Log levels
     */
    private static $levels = [
        'critical' => 5,  // Lỗi nghiêm trọng, hệ thống không hoạt động
        'error' => 4,     // Lỗi cần xử lý ngay
        'warning' => 3,   // Cảnh báo
        'notice' => 2,    // Thông báo
        'info' => 1,      // Thông tin
    ];

    /**
     * Security patterns để detect lỗi bảo mật
     */
    private static $security_patterns = [
        'SQL injection' => '/union.*select|select.*from.*where|drop\s+table|insert\s+into/i',
        'XSS attempt' => '/<script|javascript:|onerror=|onload=/i',
        'Path traversal' => '/\.\.[\/\\\\]|\.\.%2f|\.\.%5c/i',
        'Auth failure' => '/authentication failed|login failed|invalid credentials|unauthorized/i',
        'File upload attack' => '/\.php\d?$|\.phtml$|\.exe$|malicious file/i',
    ];

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
        // Private constructor for singleton
    }

    /**
     * Log một lỗi
     *
     * @param string $module Module name (pos, inventory, sync, ...)
     * @param string $level Log level (error, warning, notice, critical, info)
     * @param string $message Error message
     * @param array $context Additional context data
     * @param array $options Options (file, line, function, trace)
     * @return bool
     */
    public function log($module, $level, $message, $context = [], $options = [])
    {
        try {
            // Validate module
            if (!isset(self::$modules[$module])) {
                $module = 'system';
            }

            // Validate level
            if (!isset(self::$levels[$level])) {
                $level = 'error';
            }

            // Check if this is security-related
            $is_security = $this->detect_security_issue($message, $context);
            if ($is_security) {
                // Also log to security module
                $this->write_log('security', $level, $message, $context, $options, $is_security);
            }

            // Write to module log
            return $this->write_log($module, $level, $message, $context, $options, $is_security);

        } catch (Exception $e) {
            // Fallback to error_log if our logger fails
            error_log('TGS_Error_Logger failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Write log entry to file
     */
    private function write_log($module, $level, $message, $context, $options, $security_issue = false)
    {
        $blog_id = get_current_blog_id();

        // Get user info
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        $user_login = $user ? $user->user_login : 'guest';

        // Get blog info
        global $wpdb;
        $tgs_site_code = '';
        if (is_multisite()) {
            $tgs_site_code = $wpdb->get_var($wpdb->prepare(
                "SELECT tgs_site_code FROM {$wpdb->base_prefix}blogs WHERE blog_id = %d",
                $blog_id
            ));
        }

        // Build log entry
        $log_entry = [
            'timestamp' => current_time('Y-m-d H:i:s'),
            'unix_timestamp' => time(),
            'level' => $level,
            'level_int' => self::$levels[$level],
            'module' => $module,
            'message' => $message,
            'blog_id' => $blog_id,
            'tgs_site_code' => $tgs_site_code ?: '',
            'user_id' => $user_id,
            'user_login' => $user_login,
            'ip_address' => $this->get_client_ip(),
            'request_uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
            'request_method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : '',
            'file' => isset($options['file']) ? $options['file'] : '',
            'line' => isset($options['line']) ? $options['line'] : 0,
            'function' => isset($options['function']) ? $options['function'] : '',
            'trace' => isset($options['trace']) ? $options['trace'] : '',
            'context' => $context,
            'security_issue' => $security_issue,
        ];

        // Convert to JSON line
        $json_line = json_encode($log_entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

        // Get log file path
        $log_file = $this->get_log_file_path($blog_id, $module);

        // Write to file
        $result = @file_put_contents($log_file, $json_line, FILE_APPEND | LOCK_EX);

        return $result !== false;
    }

    /**
     * Detect security issues
     */
    private function detect_security_issue($message, $context)
    {
        $full_text = $message . ' ' . json_encode($context);

        foreach (self::$security_patterns as $issue_type => $pattern) {
            if (preg_match($pattern, $full_text)) {
                return $issue_type;
            }
        }

        return false;
    }

    /**
     * Get log directory for blog and module
     */
    private function get_log_directory($blog_id, $module)
    {
        // Switch to blog context
        if (is_multisite() && $blog_id > 1) {
            switch_to_blog($blog_id);
        }

        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];

        if (is_multisite() && $blog_id > 1) {
            restore_current_blog();
        }

        // Structure: /uploads/sites/{blog_id}/tgs_logs/{module}/
        $log_dir = $base_dir . '/tgs_logs/' . $module;

        return $log_dir;
    }

    /**
     * Ensure log directory exists
     */
    private function ensure_log_directory($blog_id, $module)
    {
        $log_dir = $this->get_log_directory($blog_id, $module);

        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);

            // Create .htaccess for security
            $htaccess_file = dirname($log_dir) . '/.htaccess';
            if (!file_exists($htaccess_file)) {
                @file_put_contents($htaccess_file, "Deny from all\n");
            }

            // Create index.php
            $index_file = dirname($log_dir) . '/index.php';
            if (!file_exists($index_file)) {
                @file_put_contents($index_file, "<?php\n// Silence is golden.\n");
            }
        }

        return $log_dir;
    }

    /**
     * Get log file path
     */
    private function get_log_file_path($blog_id, $module, $date = null)
    {
        if ($date === null) {
            $date = current_time('Y-m-d');
        }

        $log_dir = $this->ensure_log_directory($blog_id, $module);
        return $log_dir . '/' . $date . '.jsonl';
    }

    /**
     * Get client IP
     */
    private function get_client_ip()
    {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * Create base directories for all modules
     */
    public function ensure_base_directories()
    {
        $blog_id = get_current_blog_id();
        foreach (array_keys(self::$modules) as $module) {
            $this->ensure_log_directory($blog_id, $module);
        }
    }

    /**
     * Get list of supported modules
     */
    public static function get_modules()
    {
        return self::$modules;
    }

    /**
     * Get list of log levels
     */
    public static function get_levels()
    {
        return self::$levels;
    }

    /**
     * Auto-detect module from file path
     */
    public static function detect_module_from_path($file_path)
    {
        // Plugin detection
        if (stripos($file_path, 'tgs_pos') !== false || stripos($file_path, 'tgs-pos') !== false) {
            return 'pos';
        }
        if (stripos($file_path, 'tgs_shop_management') !== false || stripos($file_path, 'tgs-shop-management') !== false) {
            return 'shop';
        }
        if (stripos($file_path, 'tgs_selling_policy') !== false || stripos($file_path, 'tgs-selling-policy') !== false) {
            return 'selling_policy';
        }
        if (stripos($file_path, 'tgs_purchase_management') !== false || stripos($file_path, 'tgs-purchase-management') !== false) {
            return 'purchase';
        }

        // Feature detection
        if (stripos($file_path, 'sync') !== false || stripos($file_path, 'htsoft') !== false) {
            return 'sync';
        }
        if (stripos($file_path, 'api') !== false || stripos($file_path, 'rest') !== false) {
            return 'api';
        }
        if (stripos($file_path, 'payment') !== false) {
            return 'payment';
        }
        if (stripos($file_path, 'auth') !== false || stripos($file_path, 'login') !== false) {
            return 'auth';
        }

        return 'system';
    }

    /**
     * Cleanup old logs
     */
    public function cleanup_old_logs($days_to_keep = 90)
    {
        $blog_id = get_current_blog_id();
        $cutoff_time = time() - ($days_to_keep * 86400);

        foreach (array_keys(self::$modules) as $module) {
            $log_dir = $this->get_log_directory($blog_id, $module);

            if (!is_dir($log_dir)) {
                continue;
            }

            $handle = opendir($log_dir);
            if ($handle) {
                while (($file = readdir($handle)) !== false) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'jsonl') {
                        $filepath = $log_dir . '/' . $file;
                        if (filemtime($filepath) < $cutoff_time) {
                            @unlink($filepath);
                        }
                    }
                }
                closedir($handle);
            }
        }
    }
}
