<?php
/**
 * TGS Error Logger Integration - POS
 *
 * Tích hợp logging vào tgs_pos
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGS_Error_Logger_Integration_POS
{
    /**
     * Init integration
     */
    public static function init()
    {
        add_action('init', [__CLASS__, 'hook_ajax_handlers'], 999);
    }

    /**
     * Hook vào các AJAX handlers
     */
    public static function hook_ajax_handlers()
    {
        // Order operations
        add_action('wp_ajax_tgs_pos_save_order', [__CLASS__, 'log_before_save_order'], 1);
        add_action('wp_ajax_tgs_pos_mark_order_printed', [__CLASS__, 'log_before_mark_printed'], 1);

        // Customer operations
        add_action('wp_ajax_tgs_pos_save_customer', [__CLASS__, 'log_before_save_customer'], 1);
        add_action('wp_ajax_tgs_pos_search_customer', [__CLASS__, 'log_before_search_customer'], 1);

        // Product search
        add_action('wp_ajax_tgs_pos_search_products', [__CLASS__, 'log_before_search_products'], 1);

        // Return operations
        add_action('wp_ajax_tgs_pos_save_return', [__CLASS__, 'log_before_save_return'], 1);

        // Transfer operations
        add_action('wp_ajax_tgs_pos_save_transfer', [__CLASS__, 'log_before_save_transfer'], 1);

        // PO Request
        add_action('wp_ajax_tgs_pos_save_po_request', [__CLASS__, 'log_before_save_po_request'], 1);

        // Internal Transfer
        add_action('wp_ajax_tgs_pos_save_internal_transfer', [__CLASS__, 'log_before_save_internal_transfer'], 1);

        // Settings
        add_action('wp_ajax_tgs_pos_save_receipt_settings', [__CLASS__, 'log_before_save_settings'], 1);
        add_action('wp_ajax_tgs_pos_save_theme_color', [__CLASS__, 'log_before_save_theme'], 1);

        // Dashboard
        add_action('wp_ajax_tgs_pos_get_dashboard_stats', [__CLASS__, 'log_before_dashboard'], 1);

        // Export
        add_action('wp_ajax_tgs_pos_export_orders', [__CLASS__, 'log_before_export'], 1);
        add_action('wp_ajax_tgs_pos_stock_max_export', [__CLASS__, 'log_before_stock_max_export'], 1);
    }

    /**
     * Log before save order
     */
    public static function log_before_save_order()
    {
        try {
            $items = json_decode(stripslashes($_POST['items'] ?? '[]'), true);
            $total = floatval($_POST['total'] ?? 0);

            tgs_log_info('pos', 'Order save started', [
                'items_count' => count($items),
                'total' => $total,
                'payment_method' => sanitize_text_field($_POST['payment_method'] ?? ''),
                'customer_id' => intval($_POST['customer_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before mark printed
     */
    public static function log_before_mark_printed()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);

            tgs_log_info('pos', 'Order marked as printed', [
                'order_id' => $order_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save customer
     */
    public static function log_before_save_customer()
    {
        try {
            tgs_log_info('pos', 'Customer save started', [
                'customer_name' => sanitize_text_field($_POST['customer_name'] ?? ''),
                'customer_phone' => sanitize_text_field($_POST['customer_phone'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before search customer
     */
    public static function log_before_search_customer()
    {
        // Không log search - quá nhiều requests
    }

    /**
     * Log before search products
     */
    public static function log_before_search_products()
    {
        // Không log search - quá nhiều requests
    }

    /**
     * Log before save return
     */
    public static function log_before_save_return()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);
            $items = json_decode(stripslashes($_POST['items'] ?? '[]'), true);

            tgs_log_warning('pos', 'Return order started', [
                'order_id' => $order_id,
                'items_count' => count($items),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save transfer
     */
    public static function log_before_save_transfer()
    {
        try {
            $items = json_decode(stripslashes($_POST['items'] ?? '[]'), true);

            tgs_log_info('pos', 'Transfer created', [
                'items_count' => count($items),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save PO request
     */
    public static function log_before_save_po_request()
    {
        try {
            $items = json_decode(stripslashes($_POST['items'] ?? '[]'), true);

            tgs_log_info('pos', 'PO request created', [
                'items_count' => count($items),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save internal transfer
     */
    public static function log_before_save_internal_transfer()
    {
        try {
            $items = json_decode(stripslashes($_POST['items'] ?? '[]'), true);

            tgs_log_info('pos', 'Internal transfer created', [
                'items_count' => count($items),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save settings
     */
    public static function log_before_save_settings()
    {
        try {
            tgs_log_info('pos', 'Receipt settings updated', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save theme
     */
    public static function log_before_save_theme()
    {
        try {
            tgs_log_info('pos', 'Theme color updated', [
                'color' => sanitize_text_field($_POST['color'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before dashboard
     */
    public static function log_before_dashboard()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before export
     */
    public static function log_before_export()
    {
        try {
            tgs_log_info('pos', 'Orders export started', [
                'date_from' => sanitize_text_field($_POST['date_from'] ?? ''),
                'date_to' => sanitize_text_field($_POST['date_to'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before stock max export
     */
    public static function log_before_stock_max_export()
    {
        try {
            tgs_log_info('pos', 'Stock max export started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Wrapper để log từ plugin
     */
    public static function log_error_from_plugin($message, $context = [])
    {
        tgs_log_pos_error($message, $context);
    }

    /**
     * Wrapper để log critical
     */
    public static function log_critical_from_plugin($message, $context = [])
    {
        tgs_log_critical('pos', $message, $context);
    }
}

// Hooks để plugin có thể gọi
add_action('tgs_pos_error', ['TGS_Error_Logger_Integration_POS', 'log_error_from_plugin'], 10, 2);
add_action('tgs_pos_critical', ['TGS_Error_Logger_Integration_POS', 'log_critical_from_plugin'], 10, 2);

// Init
TGS_Error_Logger_Integration_POS::init();
