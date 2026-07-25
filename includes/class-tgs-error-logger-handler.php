<?php

/**
 * TGS Error Logger Handler
 *
 * Hook vào WordPress error handling để tự động capture lỗi
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGS_Error_Logger_Handler
{
    /**
     * Initialize error handler
     */
    public static function init()
    {
        // Set custom error handler
        set_error_handler([__CLASS__, 'handle_error']);

        // Set custom exception handler
        set_exception_handler([__CLASS__, 'handle_exception']);

        // Register shutdown function to catch fatal errors
        register_shutdown_function([__CLASS__, 'handle_shutdown']);

        // Hook into WordPress database errors
        add_action('wp_db_error', [__CLASS__, 'handle_db_error'], 10, 1);

        // Hook into AJAX errors
        add_action('wp_ajax_*', [__CLASS__, 'setup_ajax_error_capture'], 0);
        add_action('wp_ajax_nopriv_*', [__CLASS__, 'setup_ajax_error_capture'], 0);
    }

    /**
     * Handle PHP errors
     */
    public static function handle_error($errno, $errstr, $errfile = '', $errline = 0)
    {
        // Don't log if error reporting is turned off
        if (!(error_reporting() & $errno)) {
            return false;
        }

        // Map error number to level
        $level = self::map_error_level($errno);

        // Skip notices if not in debug mode (optional)
        if ($level === 'notice' && !WP_DEBUG) {
            return false;
        }

        // Auto-detect module from file path
        $module = TGS_Error_Logger::detect_module_from_path($errfile);

        // Build context
        $context = [
            'error_type' => self::get_error_type_name($errno),
            'error_code' => $errno,
        ];

        // Build options
        $options = [
            'file' => $errfile,
            'line' => $errline,
            'function' => '',
            'trace' => self::get_backtrace(2),
        ];

        // Log the error
        TGS_Error_Logger::instance()->log(
            $module,
            $level,
            $errstr,
            $context,
            $options
        );

        // Don't execute PHP internal error handler
        return true;
    }

    /**
     * Handle uncaught exceptions
     */
    public static function handle_exception($exception)
    {
        // Auto-detect module from file path
        $module = TGS_Error_Logger::detect_module_from_path($exception->getFile());

        // Build context
        $context = [
            'exception_class' => get_class($exception),
            'exception_code' => $exception->getCode(),
        ];

        // Build options
        $options = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'function' => '',
            'trace' => $exception->getTraceAsString(),
        ];

        // Log the exception
        TGS_Error_Logger::instance()->log(
            $module,
            'critical',
            $exception->getMessage(),
            $context,
            $options
        );
    }

    /**
     * Handle fatal errors during shutdown
     */
    public static function handle_shutdown()
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        // Only log fatal errors
        if (!in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            return;
        }

        // Auto-detect module
        $module = TGS_Error_Logger::detect_module_from_path($error['file']);

        // Build context
        $context = [
            'error_type' => self::get_error_type_name($error['type']),
            'error_code' => $error['type'],
            'is_fatal' => true,
        ];

        // Build options
        $options = [
            'file' => $error['file'],
            'line' => $error['line'],
            'function' => '',
            'trace' => '',
        ];

        // Log the fatal error
        TGS_Error_Logger::instance()->log(
            $module,
            'critical',
            $error['message'],
            $context,
            $options
        );
    }

    /**
     * Handle WordPress database errors
     */
    public static function handle_db_error($error)
    {
        global $wpdb;

        // Get last query
        $last_query = $wpdb->last_query;

        // Build context
        $context = [
            'query' => $last_query,
            'db_error' => $wpdb->last_error,
        ];

        // Log to database module
        TGS_Error_Logger::instance()->log(
            'database',
            'error',
            'Database error: ' . $error,
            $context,
            [
                'file' => '',
                'line' => 0,
                'function' => '',
                'trace' => self::get_backtrace(2),
            ]
        );
    }

    /**
     * Setup error capture for AJAX requests
     */
    public static function setup_ajax_error_capture()
    {
        // Enable error capture context for AJAX
        add_filter('wp_die_ajax_handler', function($handler) {
            return function($message, $title = '', $args = []) use ($handler) {
                // Log AJAX error
                TGS_Error_Logger::instance()->log(
                    'api',
                    'error',
                    'AJAX error: ' . $message,
                    [
                        'title' => $title,
                        'action' => isset($_REQUEST['action']) ? $_REQUEST['action'] : '',
                    ],
                    [
                        'file' => '',
                        'line' => 0,
                        'function' => '',
                        'trace' => self::get_backtrace(3),
                    ]
                );

                // Call original handler
                return call_user_func($handler, $message, $title, $args);
            };
        });
    }

    /**
     * Map PHP error number to log level
     */
    private static function map_error_level($errno)
    {
        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                return 'critical';

            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return 'error';

            case E_NOTICE:
            case E_USER_NOTICE:
                return 'notice';

            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'warning';

            case E_STRICT:
                return 'info';

            default:
                return 'error';
        }
    }

    /**
     * Get error type name
     */
    private static function get_error_type_name($errno)
    {
        $error_types = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        return isset($error_types[$errno]) ? $error_types[$errno] : 'UNKNOWN';
    }

    /**
     * Get simplified backtrace
     */
    private static function get_backtrace($skip = 0)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

        // Skip first N frames
        $trace = array_slice($trace, $skip);

        // Format trace
        $formatted = [];
        foreach ($trace as $frame) {
            $line = '';

            if (isset($frame['file'])) {
                $line .= basename($frame['file']);
            }

            if (isset($frame['line'])) {
                $line .= ':' . $frame['line'];
            }

            if (isset($frame['function'])) {
                $line .= ' ' . $frame['function'] . '()';
            }

            if (!empty($line)) {
                $formatted[] = $line;
            }
        }

        return implode(' -> ', $formatted);
    }

    /**
     * Manual log helper - for explicit logging in code
     */
    public static function log_error($module, $message, $context = [], $level = 'error')
    {
        // Get caller info
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($backtrace[1]) ? $backtrace[1] : [];

        $options = [
            'file' => isset($caller['file']) ? $caller['file'] : '',
            'line' => isset($caller['line']) ? $caller['line'] : 0,
            'function' => isset($caller['function']) ? $caller['function'] : '',
            'trace' => self::get_backtrace(2),
        ];

        return TGS_Error_Logger::instance()->log($module, $level, $message, $context, $options);
    }
}
