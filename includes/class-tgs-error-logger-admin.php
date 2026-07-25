<?php

/**
 * TGS Error Logger Admin
 *
 * Quản lý menu và UI admin
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGS_Error_Logger_Admin
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
        add_action('admin_menu', [$this, 'add_menu'], 100);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        // AJAX handlers
        add_action('wp_ajax_tgs_error_logger_get_logs', [$this, 'ajax_get_logs']);
        add_action('wp_ajax_tgs_error_logger_download_log', [$this, 'ajax_download_log']);
        add_action('wp_ajax_tgs_error_logger_clear_logs', [$this, 'ajax_clear_logs']);
    }

    /**
     * Add menu
     */
    public function add_menu()
    {
        global $submenu;

        // Parent menu slug
        $parent_slug = 'tgs-shop-management';

        // Check if parent exists
        if (!isset($submenu[$parent_slug])) {
            // Create standalone menu
            add_menu_page(
                'Nhật ký lỗi',
                'Nhật ký lỗi',
                'manage_options',
                'tgs-error-logger',
                [$this, 'render_admin_page'],
                'dashicons-warning',
                57
            );
        } else {
            // Add as submenu
            add_submenu_page(
                $parent_slug,
                'Nhật ký lỗi',
                'Nhật ký lỗi',
                'manage_options',
                'tgs-error-logger',
                [$this, 'render_admin_page']
            );
        }
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts($hook)
    {
        if (strpos($hook, 'tgs-error-logger') === false) {
            return;
        }

        wp_enqueue_style(
            'tgs-error-logger-admin',
            TGS_ERROR_LOGGER_URL . 'assets/admin.css',
            [],
            TGS_ERROR_LOGGER_VERSION
        );

        wp_enqueue_script(
            'tgs-error-logger-admin',
            TGS_ERROR_LOGGER_URL . 'assets/admin.js',
            ['jquery'],
            TGS_ERROR_LOGGER_VERSION,
            true
        );

        wp_localize_script('tgs-error-logger-admin', 'tgsErrorLogger', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tgs_error_logger_nonce'),
        ]);
    }

    /**
     * Render admin page
     */
    public function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Bạn không có quyền truy cập trang này.');
        }

        include TGS_ERROR_LOGGER_PATH . 'templates/admin-page.php';
    }

    /**
     * AJAX: Get logs
     */
    public function ajax_get_logs()
    {
        check_ajax_referer('tgs_error_logger_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Không có quyền']);
        }

        $blog_id = isset($_POST['blog_id']) ? intval($_POST['blog_id']) : get_current_blog_id();
        $module = isset($_POST['module']) ? sanitize_text_field($_POST['module']) : 'system';
        $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : current_time('Y-m-d');
        $level = isset($_POST['level']) ? sanitize_text_field($_POST['level']) : '';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $security_only = isset($_POST['security_only']) && $_POST['security_only'] === 'true';

        $reader = TGS_Error_Logger_Reader::instance();

        $filters = [];
        if ($level) {
            $filters['level'] = $level;
        }
        if ($search) {
            $filters['search'] = $search;
        }
        if ($security_only) {
            $filters['security_only'] = true;
        }

        $logs = $reader->read_logs($blog_id, $module, $date, $filters);

        wp_send_json_success([
            'logs' => $logs,
            'count' => count($logs),
        ]);
    }

    /**
     * AJAX: Download log file
     */
    public function ajax_download_log()
    {
        check_ajax_referer('tgs_error_logger_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Không có quyền');
        }

        $blog_id = isset($_GET['blog_id']) ? intval($_GET['blog_id']) : get_current_blog_id();
        $module = isset($_GET['module']) ? sanitize_text_field($_GET['module']) : '';
        $date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';

        $reader = TGS_Error_Logger_Reader::instance();
        $log_dir = $reader->get_log_directory($blog_id, $module);
        $file_path = $log_dir . '/' . $date . '.jsonl';

        if (!file_exists($file_path)) {
            wp_die('File không tồn tại');
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $module . '_' . $date . '.jsonl"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }

    /**
     * AJAX: Clear old logs
     */
    public function ajax_clear_logs()
    {
        check_ajax_referer('tgs_error_logger_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Không có quyền']);
        }

        $days = isset($_POST['days']) ? intval($_POST['days']) : 90;

        TGS_Error_Logger::instance()->cleanup_old_logs($days);

        wp_send_json_success([
            'message' => 'Đã xóa log cũ hơn ' . $days . ' ngày',
        ]);
    }
}
