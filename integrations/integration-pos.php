<?php
/**
 * TGS Error Logger Integration - POS
 *
 * Tích hợp logging vào tgs_pos - ĐẦY ĐỦ VÀ ĐÚNG
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
     * Hook vào các AJAX handlers - CHỈ action thực sự tồn tại
     */
    public static function hook_ajax_handlers()
    {
        // Order operations
        add_action('wp_ajax_tgs_pos_save_order', [__CLASS__, 'log_before_save_order'], 1);
        add_action('wp_ajax_tgs_pos_mark_order_printed', [__CLASS__, 'log_before_mark_printed'], 1);
        add_action('wp_ajax_tgs_pos_list_orders', [__CLASS__, 'log_before_list_orders'], 1);
        add_action('wp_ajax_tgs_pos_get_order_detail', [__CLASS__, 'log_before_get_order_detail'], 1);

        // Customer operations
        add_action('wp_ajax_tgs_pos_save_customer', [__CLASS__, 'log_before_save_customer'], 1);
        add_action('wp_ajax_tgs_pos_search_customer', [__CLASS__, 'log_before_search_customer'], 1);
        add_action('wp_ajax_tgs_pos_get_customer_points', [__CLASS__, 'log_before_get_customer_points'], 1);
        add_action('wp_ajax_tgs_pos_get_customer_orders', [__CLASS__, 'log_before_get_customer_orders'], 1);
        add_action('wp_ajax_tgs_pos_get_today_customers', [__CLASS__, 'log_before_get_today_customers'], 1);

        // Product search & info
        add_action('wp_ajax_tgs_pos_search_products', [__CLASS__, 'log_before_search_products'], 1);
        add_action('wp_ajax_tgs_pos_search_by_lot_barcode', [__CLASS__, 'log_before_search_by_lot_barcode'], 1);
        add_action('wp_ajax_tgs_pos_get_product_info', [__CLASS__, 'log_before_get_product_info'], 1);
        add_action('wp_ajax_tgs_pos_get_product_lots', [__CLASS__, 'log_before_get_product_lots'], 1);
        add_action('wp_ajax_tgs_pos_get_categories', [__CLASS__, 'log_before_get_categories'], 1);

        // Return operations
        add_action('wp_ajax_tgs_pos_save_return', [__CLASS__, 'log_before_save_return'], 1);
        add_action('wp_ajax_tgs_pos_update_return', [__CLASS__, 'log_before_update_return'], 1);
        add_action('wp_ajax_tgs_pos_prepare_return_order', [__CLASS__, 'log_before_prepare_return'], 1);
        add_action('wp_ajax_tgs_pos_commit_return_order', [__CLASS__, 'log_before_commit_return'], 1);
        add_action('wp_ajax_tgs_pos_list_return_orders', [__CLASS__, 'log_before_list_returns'], 1);
        add_action('wp_ajax_tgs_pos_get_return_detail', [__CLASS__, 'log_before_get_return_detail'], 1);

        // Exchange operations
        add_action('wp_ajax_tgs_pos_exchange_list_customer_orders', [__CLASS__, 'log_before_exchange_list_orders'], 1);
        add_action('wp_ajax_tgs_pos_exchange_prepare_sale', [__CLASS__, 'log_before_prepare_exchange'], 1);
        add_action('wp_ajax_tgs_pos_exchange_commit', [__CLASS__, 'log_before_commit_exchange'], 1);

        // Transfer operations
        add_action('wp_ajax_tgs_pos_save_transfer', [__CLASS__, 'log_before_save_transfer'], 1);
        add_action('wp_ajax_tgs_pos_update_transfer', [__CLASS__, 'log_before_update_transfer'], 1);

        // Internal Transfer operations
        add_action('wp_ajax_tgs_pos_save_internal_transfer', [__CLASS__, 'log_before_save_internal_transfer'], 1);
        add_action('wp_ajax_tgs_pos_update_internal_transfer', [__CLASS__, 'log_before_update_internal_transfer'], 1);
        add_action('wp_ajax_tgs_pos_internal_transfer_list', [__CLASS__, 'log_before_internal_transfer_list'], 1);
        add_action('wp_ajax_tgs_pos_internal_transfer_detail', [__CLASS__, 'log_before_internal_transfer_detail'], 1);
        add_action('wp_ajax_tgs_pos_internal_transfer_approve', [__CLASS__, 'log_before_approve_internal_transfer'], 1);
        add_action('wp_ajax_tgs_pos_internal_transfer_reject', [__CLASS__, 'log_before_reject_internal_transfer'], 1);

        // PO Request operations
        add_action('wp_ajax_tgs_pos_save_po_request', [__CLASS__, 'log_before_save_po_request'], 1);
        add_action('wp_ajax_tgs_pos_update_po_request', [__CLASS__, 'log_before_update_po_request'], 1);
        add_action('wp_ajax_tgs_pos_poa_create_request', [__CLASS__, 'log_before_poa_create_request'], 1);
        add_action('wp_ajax_tgs_pos_poa_list_requests', [__CLASS__, 'log_before_poa_list_requests'], 1);
        add_action('wp_ajax_tgs_pos_poa_get_request_detail', [__CLASS__, 'log_before_poa_get_request_detail'], 1);

        // Payment operations
        add_action('wp_ajax_tgs_pos_get_payment_gateways', [__CLASS__, 'log_before_get_payment_gateways'], 1);
        add_action('wp_ajax_tgs_pos_check_payment_status', [__CLASS__, 'log_before_check_payment_status'], 1);

        // Promotion & Coupon operations
        add_action('wp_ajax_tgs_pos_apply_coupon', [__CLASS__, 'log_before_apply_coupon'], 1);
        add_action('wp_ajax_tgs_pos_check_promotions', [__CLASS__, 'log_before_check_promotions'], 1);

        // Settings
        add_action('wp_ajax_tgs_pos_save_receipt_settings', [__CLASS__, 'log_before_save_settings'], 1);
        add_action('wp_ajax_tgs_pos_save_theme_color', [__CLASS__, 'log_before_save_theme'], 1);

        // Site permissions
        add_action('wp_ajax_tgs_pos_save_site_permission', [__CLASS__, 'log_before_save_site_permission'], 1);
        add_action('wp_ajax_tgs_pos_toggle_permission_mode', [__CLASS__, 'log_before_toggle_permission_mode'], 1);

        // Dashboard & Stats
        add_action('wp_ajax_tgs_pos_get_dashboard_stats', [__CLASS__, 'log_before_dashboard'], 1);
        add_action('wp_ajax_tgs_pos_get_policy_stats', [__CLASS__, 'log_before_get_policy_stats'], 1);

        // Export operations
        add_action('wp_ajax_tgs_pos_export_orders', [__CLASS__, 'log_before_export'], 1);
        add_action('wp_ajax_tgs_pos_stock_max_export', [__CLASS__, 'log_before_stock_max_export'], 1);

        // Realtime inventory sync
        add_action('wp_ajax_tgs_pos_get_realtime_inventory', [__CLASS__, 'log_before_get_realtime_inventory'], 1);

        // HTSoft integration
        add_action('wp_ajax_tgs_pos_htsoft_map_skus', [__CLASS__, 'log_before_htsoft_map_skus'], 1);
    }

    // === ORDER OPERATIONS ===

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

    public static function log_before_list_orders()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_get_order_detail()
    {
        // Không log - quá nhiều requests
    }

    // === CUSTOMER OPERATIONS ===

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

    public static function log_before_search_customer()
    {
        // Không log search - quá nhiều requests
    }

    public static function log_before_get_customer_points()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_get_customer_orders()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_get_today_customers()
    {
        // Không log - quá nhiều requests
    }

    // === PRODUCT OPERATIONS ===

    public static function log_before_search_products()
    {
        // Không log search - quá nhiều requests
    }

    public static function log_before_search_by_lot_barcode()
    {
        // Không log search - quá nhiều requests
    }

    public static function log_before_get_product_info()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_get_product_lots()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_get_categories()
    {
        // Không log - quá nhiều requests
    }

    // === RETURN OPERATIONS ===

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

    public static function log_before_update_return()
    {
        try {
            $return_id = intval($_POST['return_id'] ?? 0);

            tgs_log_info('pos', 'Return order updated', [
                'return_id' => $return_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_prepare_return()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);

            tgs_log_info('pos', 'Return preparation started', [
                'order_id' => $order_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_commit_return()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);

            tgs_log_warning('pos', 'Return committed', [
                'order_id' => $order_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_list_returns()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_get_return_detail()
    {
        // Không log - quá nhiều requests
    }

    // === EXCHANGE OPERATIONS ===

    public static function log_before_exchange_list_orders()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_prepare_exchange()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);

            tgs_log_info('pos', 'Exchange preparation started', [
                'order_id' => $order_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_commit_exchange()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);

            tgs_log_warning('pos', 'Exchange committed', [
                'order_id' => $order_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    // === TRANSFER OPERATIONS ===

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

    public static function log_before_update_transfer()
    {
        try {
            $transfer_id = intval($_POST['transfer_id'] ?? 0);

            tgs_log_info('pos', 'Transfer updated', [
                'transfer_id' => $transfer_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    // === INTERNAL TRANSFER OPERATIONS ===

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

    public static function log_before_update_internal_transfer()
    {
        try {
            $transfer_id = intval($_POST['transfer_id'] ?? 0);

            tgs_log_info('pos', 'Internal transfer updated', [
                'transfer_id' => $transfer_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_internal_transfer_list()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_internal_transfer_detail()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_approve_internal_transfer()
    {
        try {
            $transfer_id = intval($_POST['transfer_id'] ?? 0);

            tgs_log_info('pos', 'Internal transfer approved', [
                'transfer_id' => $transfer_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_reject_internal_transfer()
    {
        try {
            $transfer_id = intval($_POST['transfer_id'] ?? 0);

            tgs_log_warning('pos', 'Internal transfer rejected', [
                'transfer_id' => $transfer_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    // === PO REQUEST OPERATIONS ===

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

    public static function log_before_update_po_request()
    {
        try {
            $request_id = intval($_POST['request_id'] ?? 0);

            tgs_log_info('pos', 'PO request updated', [
                'request_id' => $request_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_poa_create_request()
    {
        try {
            tgs_log_info('pos', 'POA request created', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_poa_list_requests()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_poa_get_request_detail()
    {
        // Không log - quá nhiều requests
    }

    // === PAYMENT OPERATIONS ===

    public static function log_before_get_payment_gateways()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_check_payment_status()
    {
        // Không log - quá nhiều requests
    }

    // === PROMOTION & COUPON OPERATIONS ===

    public static function log_before_apply_coupon()
    {
        try {
            $coupon_code = sanitize_text_field($_POST['coupon_code'] ?? '');

            tgs_log_info('pos', 'Coupon applied', [
                'coupon_code' => $coupon_code,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_check_promotions()
    {
        // Không log - quá nhiều requests
    }

    // === SETTINGS ===

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

    // === SITE PERMISSIONS ===

    public static function log_before_save_site_permission()
    {
        try {
            $site_id = intval($_POST['site_id'] ?? 0);

            tgs_log_info('pos', 'Site permission updated', [
                'site_id' => $site_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_toggle_permission_mode()
    {
        try {
            $mode = sanitize_text_field($_POST['mode'] ?? '');

            tgs_log_info('pos', 'Permission mode toggled', [
                'mode' => $mode,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    // === DASHBOARD & STATS ===

    public static function log_before_dashboard()
    {
        // Không log - quá nhiều requests
    }

    public static function log_before_get_policy_stats()
    {
        // Không log - quá nhiều requests
    }

    // === EXPORT OPERATIONS ===

    public static function log_before_export()
    {
        try {
            tgs_log_info('pos', 'Orders export started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

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

    // === REALTIME INVENTORY ===

    public static function log_before_get_realtime_inventory()
    {
        // Không log - quá nhiều requests
    }

    // === HTSOFT INTEGRATION ===

    public static function log_before_htsoft_map_skus()
    {
        try {
            tgs_log_info('pos', 'HTSoft SKU mapping started', [
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
