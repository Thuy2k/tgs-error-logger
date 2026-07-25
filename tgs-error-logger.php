<?php
/**
 * Plugin Name: TGS - Error Logger
 * Description: Hệ thống log lỗi thông minh, chia theo module và dễ quản lý
 * Version: 1.0.0
 * Author: TGS Team
 * Text Domain: tgs-error-logger
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('TGS_ERROR_LOGGER_PATH', plugin_dir_path(__FILE__));
define('TGS_ERROR_LOGGER_URL', plugin_dir_url(__FILE__));
define('TGS_ERROR_LOGGER_VERSION', '1.0.0');

// Load classes
require_once TGS_ERROR_LOGGER_PATH . 'includes/class-tgs-error-logger.php';
require_once TGS_ERROR_LOGGER_PATH . 'includes/class-tgs-error-logger-handler.php';
require_once TGS_ERROR_LOGGER_PATH . 'includes/class-tgs-error-logger-reader.php';
require_once TGS_ERROR_LOGGER_PATH . 'includes/class-tgs-error-logger-admin.php';

// Load helper functions
require_once TGS_ERROR_LOGGER_PATH . 'includes/helpers.php';

// Initialize
add_action('plugins_loaded', function() {
    // Initialize error handler
    TGS_Error_Logger_Handler::init();

    // Initialize admin UI
    if (is_admin()) {
        TGS_Error_Logger_Admin::instance();
    }
}, 1);

// Activation hook
register_activation_hook(__FILE__, function() {
    // Create log directories for main site
    TGS_Error_Logger::instance()->ensure_base_directories();
});
