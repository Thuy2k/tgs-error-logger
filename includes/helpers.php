<?php

/**
 * TGS Error Logger - Helper Functions
 *
 * Các function tiện ích để dễ dàng sử dụng logger
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Log error
 *
 * @param string $module Module name
 * @param string $message Error message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_error($module, $message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error($module, $message, $context, 'error');
}

/**
 * Log warning
 *
 * @param string $module Module name
 * @param string $message Warning message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_warning($module, $message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error($module, $message, $context, 'warning');
}

/**
 * Log critical error
 *
 * @param string $module Module name
 * @param string $message Critical error message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_critical($module, $message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error($module, $message, $context, 'critical');
}

/**
 * Log notice
 *
 * @param string $module Module name
 * @param string $message Notice message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_notice($module, $message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error($module, $message, $context, 'notice');
}

/**
 * Log info
 *
 * @param string $module Module name
 * @param string $message Info message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_info($module, $message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error($module, $message, $context, 'info');
}

/**
 * Log security issue
 *
 * @param string $message Security issue message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_security($message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error('security', $message, $context, 'critical');
}

/**
 * Log database error
 *
 * @param string $message Database error message
 * @param array $context Additional context (query, etc)
 * @return bool
 */
function tgs_log_database_error($message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error('database', $message, $context, 'error');
}

/**
 * Log API error
 *
 * @param string $message API error message
 * @param array $context Additional context (endpoint, response, etc)
 * @return bool
 */
function tgs_log_api_error($message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error('api', $message, $context, 'error');
}

/**
 * Log POS error
 *
 * @param string $message POS error message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_pos_error($message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error('pos', $message, $context, 'error');
}

/**
 * Log sync error
 *
 * @param string $message Sync error message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_sync_error($message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error('sync', $message, $context, 'error');
}

/**
 * Wrap function execution with error logging
 *
 * @param string $module Module name
 * @param callable $callback Function to execute
 * @param array $args Arguments to pass to callback
 * @return mixed Return value from callback or false on error
 */
function tgs_with_error_logging($module, $callback, $args = [])
{
    try {
        return call_user_func_array($callback, $args);
    } catch (Exception $e) {
        tgs_log_error($module, $e->getMessage(), [
            'exception_class' => get_class($e),
            'exception_code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
        return false;
    } catch (Error $e) {
        tgs_log_critical($module, $e->getMessage(), [
            'error_class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
        return false;
    }
}

/**
 * Get error logger instance
 *
 * @return TGS_Error_Logger
 */
function tgs_error_logger()
{
    return TGS_Error_Logger::instance();
}

/**
 * Get error logger reader instance
 *
 * @return TGS_Error_Logger_Reader
 */
function tgs_error_logger_reader()
{
    return TGS_Error_Logger_Reader::instance();
}

/**
 * Log shop management error
 *
 * @param string $message Error message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_shop_error($message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error('shop', $message, $context, 'error');
}

/**
 * Log selling policy error
 *
 * @param string $message Error message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_selling_policy_error($message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error('selling_policy', $message, $context, 'error');
}

/**
 * Log purchase management error
 *
 * @param string $message Error message
 * @param array $context Additional context
 * @return bool
 */
function tgs_log_purchase_error($message, $context = [])
{
    return TGS_Error_Logger_Handler::log_error('purchase', $message, $context, 'error');
}
