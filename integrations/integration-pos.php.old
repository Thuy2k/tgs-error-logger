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
        add_action('wp_ajax_tgs_pos_get_orders', [__CLASS__, 'log_before_get_orders'], 1);
        add_action('wp_ajax_tgs_pos_get_order_detail', [__CLASS__, 'log_before_get_order_detail'], 1);
        add_action('wp_ajax_tgs_pos_delete_order', [__CLASS__, 'log_before_delete_order'], 1);
        add_action('wp_ajax_tgs_pos_update_order_status', [__CLASS__, 'log_before_update_order_status'], 1);

        // Customer operations
        add_action('wp_ajax_tgs_pos_save_customer', [__CLASS__, 'log_before_save_customer'], 1);
        add_action('wp_ajax_tgs_pos_search_customer', [__CLASS__, 'log_before_search_customer'], 1);
        add_action('wp_ajax_tgs_pos_get_customer_points', [__CLASS__, 'log_before_get_customer_points'], 1);
        add_action('wp_ajax_tgs_pos_get_customer_orders', [__CLASS__, 'log_before_get_customer_orders'], 1);
        add_action('wp_ajax_tgs_pos_get_today_customers', [__CLASS__, 'log_before_get_today_customers'], 1);
        add_action('wp_ajax_tgs_pos_update_customer_points', [__CLASS__, 'log_before_update_customer_points'], 1);

        // Product search & info
        add_action('wp_ajax_tgs_pos_search_products', [__CLASS__, 'log_before_search_products'], 1);
        add_action('wp_ajax_tgs_pos_search_product_by_lot_barcode', [__CLASS__, 'log_before_search_by_lot_barcode'], 1);
        add_action('wp_ajax_tgs_pos_get_product_info', [__CLASS__, 'log_before_get_product_info'], 1);
        add_action('wp_ajax_tgs_pos_get_product_lots', [__CLASS__, 'log_before_get_product_lots'], 1);
        add_action('wp_ajax_tgs_pos_get_categories', [__CLASS__, 'log_before_get_categories'], 1);

        // Return operations
        add_action('wp_ajax_tgs_pos_save_return', [__CLASS__, 'log_before_save_return'], 1);
        add_action('wp_ajax_tgs_pos_prepare_return', [__CLASS__, 'log_before_prepare_return'], 1);
        add_action('wp_ajax_tgs_pos_commit_return', [__CLASS__, 'log_before_commit_return'], 1);
        add_action('wp_ajax_tgs_pos_get_returns', [__CLASS__, 'log_before_get_returns'], 1);
        add_action('wp_ajax_tgs_pos_get_return_detail', [__CLASS__, 'log_before_get_return_detail'], 1);

        // Exchange operations
        add_action('wp_ajax_tgs_pos_get_exchange_orders', [__CLASS__, 'log_before_get_exchange_orders'], 1);
        add_action('wp_ajax_tgs_pos_prepare_exchange', [__CLASS__, 'log_before_prepare_exchange'], 1);
        add_action('wp_ajax_tgs_pos_commit_exchange', [__CLASS__, 'log_before_commit_exchange'], 1);

        // Transfer operations
        add_action('wp_ajax_tgs_pos_save_transfer', [__CLASS__, 'log_before_save_transfer'], 1);
        add_action('wp_ajax_tgs_pos_get_transfers', [__CLASS__, 'log_before_get_transfers'], 1);
        add_action('wp_ajax_tgs_pos_get_transfer_detail', [__CLASS__, 'log_before_get_transfer_detail'], 1);

        // Internal Transfer operations
        add_action('wp_ajax_tgs_pos_save_internal_transfer', [__CLASS__, 'log_before_save_internal_transfer'], 1);
        add_action('wp_ajax_tgs_pos_get_internal_transfers', [__CLASS__, 'log_before_get_internal_transfers'], 1);
        add_action('wp_ajax_tgs_pos_approve_internal_transfer', [__CLASS__, 'log_before_approve_internal_transfer'], 1);
        add_action('wp_ajax_tgs_pos_reject_internal_transfer', [__CLASS__, 'log_before_reject_internal_transfer'], 1);

        // PO Request operations
        add_action('wp_ajax_tgs_pos_save_po_request', [__CLASS__, 'log_before_save_po_request'], 1);
        add_action('wp_ajax_tgs_pos_get_po_requests', [__CLASS__, 'log_before_get_po_requests'], 1);
        add_action('wp_ajax_tgs_pos_get_po_request_detail', [__CLASS__, 'log_before_get_po_request_detail'], 1);

        // Payment operations
        add_action('wp_ajax_tgs_pos_get_payment_gateways', [__CLASS__, 'log_before_get_payment_gateways'], 1);
        add_action('wp_ajax_tgs_pos_check_payment_status', [__CLASS__, 'log_before_check_payment_status'], 1);
        add_action('wp_ajax_tgs_pos_process_payment', [__CLASS__, 'log_before_process_payment'], 1);

        // Promotion & Coupon operations
        add_action('wp_ajax_tgs_pos_apply_coupon', [__CLASS__, 'log_before_apply_coupon'], 1);
        add_action('wp_ajax_tgs_pos_remove_coupon', [__CLASS__, 'log_before_remove_coupon'], 1);
        add_action('wp_ajax_tgs_pos_get_promotions', [__CLASS__, 'log_before_get_promotions'], 1);
        add_action('wp_ajax_tgs_pos_calculate_promotion', [__CLASS__, 'log_before_calculate_promotion'], 1);

        // Settings
        add_action('wp_ajax_tgs_pos_save_receipt_settings', [__CLASS__, 'log_before_save_settings'], 1);
        add_action('wp_ajax_tgs_pos_save_theme_color', [__CLASS__, 'log_before_save_theme'], 1);
        add_action('wp_ajax_tgs_pos_save_print_settings', [__CLASS__, 'log_before_save_print_settings'], 1);
        add_action('wp_ajax_tgs_pos_get_settings', [__CLASS__, 'log_before_get_settings'], 1);

        // Site permissions
        add_action('wp_ajax_tgs_pos_check_site_permission', [__CLASS__, 'log_before_check_site_permission'], 1);
        add_action('wp_ajax_tgs_pos_update_site_permission', [__CLASS__, 'log_before_update_site_permission'], 1);

        // Dashboard & Stats
        add_action('wp_ajax_tgs_pos_get_dashboard_stats', [__CLASS__, 'log_before_dashboard'], 1);
        add_action('wp_ajax_tgs_pos_get_sales_stats', [__CLASS__, 'log_before_get_sales_stats'], 1);
        add_action('wp_ajax_tgs_pos_get_inventory_stats', [__CLASS__, 'log_before_get_inventory_stats'], 1);

        // Export operations
        add_action('wp_ajax_tgs_pos_export_orders', [__CLASS__, 'log_before_export'], 1);
        add_action('wp_ajax_tgs_pos_stock_max_export', [__CLASS__, 'log_before_stock_max_export'], 1);
        add_action('wp_ajax_tgs_pos_export_sales_report', [__CLASS__, 'log_before_export_sales_report'], 1);
        add_action('wp_ajax_tgs_pos_export_inventory_report', [__CLASS__, 'log_before_export_inventory_report'], 1);

        // Realtime inventory sync
        add_action('wp_ajax_tgs_pos_sync_inventory_realtime', [__CLASS__, 'log_before_sync_inventory_realtime'], 1);
        add_action('wp_ajax_tgs_pos_get_inventory_status', [__CLASS__, 'log_before_get_inventory_status'], 1);

        // HTSoft integration
        add_action('wp_ajax_tgs_pos_sync_htsoft', [__CLASS__, 'log_before_sync_htsoft'], 1);
        add_action('wp_ajax_tgs_pos_push_order_to_htsoft', [__CLASS__, 'log_before_push_order_htsoft'], 1);
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

    // === NEW HANDLERS ===

    /**
     * Log before get orders
     */
    public static function log_before_get_orders()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get order detail
     */
    public static function log_before_get_order_detail()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before delete order
     */
    public static function log_before_delete_order()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);
            tgs_log_warning('pos', 'Order deleted', [
                'order_id' => $order_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before update order status
     */
    public static function log_before_update_order_status()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);
            $status = sanitize_text_field($_POST['status'] ?? '');
            tgs_log_info('pos', 'Order status updated', [
                'order_id' => $order_id,
                'status' => $status,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before get customer points
     */
    public static function log_before_get_customer_points()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get customer orders
     */
    public static function log_before_get_customer_orders()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get today customers
     */
    public static function log_before_get_today_customers()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before update customer points
     */
    public static function log_before_update_customer_points()
    {
        try {
            $customer_id = intval($_POST['customer_id'] ?? 0);
            $points = intval($_POST['points'] ?? 0);
            tgs_log_info('pos', 'Customer points updated', [
                'customer_id' => $customer_id,
                'points' => $points,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before search by lot barcode
     */
    public static function log_before_search_by_lot_barcode()
    {
        // Không log search - quá nhiều requests
    }

    /**
     * Log before get product info
     */
    public static function log_before_get_product_info()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get product lots
     */
    public static function log_before_get_product_lots()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get categories
     */
    public static function log_before_get_categories()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before prepare return
     */
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

    /**
     * Log before commit return
     */
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

    /**
     * Log before get returns
     */
    public static function log_before_get_returns()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get return detail
     */
    public static function log_before_get_return_detail()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get exchange orders
     */
    public static function log_before_get_exchange_orders()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before prepare exchange
     */
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

    /**
     * Log before commit exchange
     */
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

    /**
     * Log before get transfers
     */
    public static function log_before_get_transfers()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get transfer detail
     */
    public static function log_before_get_transfer_detail()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get internal transfers
     */
    public static function log_before_get_internal_transfers()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before approve internal transfer
     */
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

    /**
     * Log before reject internal transfer
     */
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

    /**
     * Log before get PO requests
     */
    public static function log_before_get_po_requests()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get PO request detail
     */
    public static function log_before_get_po_request_detail()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get payment gateways
     */
    public static function log_before_get_payment_gateways()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before check payment status
     */
    public static function log_before_check_payment_status()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before process payment
     */
    public static function log_before_process_payment()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);
            $gateway = sanitize_text_field($_POST['gateway'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            tgs_log_info('pos', 'Payment processing started', [
                'order_id' => $order_id,
                'gateway' => $gateway,
                'amount' => $amount,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before apply coupon
     */
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

    /**
     * Log before remove coupon
     */
    public static function log_before_remove_coupon()
    {
        try {
            $coupon_code = sanitize_text_field($_POST['coupon_code'] ?? '');
            tgs_log_info('pos', 'Coupon removed', [
                'coupon_code' => $coupon_code,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before get promotions
     */
    public static function log_before_get_promotions()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before calculate promotion
     */
    public static function log_before_calculate_promotion()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before save print settings
     */
    public static function log_before_save_print_settings()
    {
        try {
            tgs_log_info('pos', 'Print settings updated', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before get settings
     */
    public static function log_before_get_settings()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before check site permission
     */
    public static function log_before_check_site_permission()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before update site permission
     */
    public static function log_before_update_site_permission()
    {
        try {
            $site_id = intval($_POST['site_id'] ?? 0);
            $permission = sanitize_text_field($_POST['permission'] ?? '');
            tgs_log_info('pos', 'Site permission updated', [
                'site_id' => $site_id,
                'permission' => $permission,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before get sales stats
     */
    public static function log_before_get_sales_stats()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before get inventory stats
     */
    public static function log_before_get_inventory_stats()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before export sales report
     */
    public static function log_before_export_sales_report()
    {
        try {
            tgs_log_info('pos', 'Sales report export started', [
                'date_from' => sanitize_text_field($_POST['date_from'] ?? ''),
                'date_to' => sanitize_text_field($_POST['date_to'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before export inventory report
     */
    public static function log_before_export_inventory_report()
    {
        try {
            tgs_log_info('pos', 'Inventory report export started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before sync inventory realtime
     */
    public static function log_before_sync_inventory_realtime()
    {
        try {
            tgs_log_info('pos', 'Realtime inventory sync started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before get inventory status
     */
    public static function log_before_get_inventory_status()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before sync htsoft
     */
    public static function log_before_sync_htsoft()
    {
        try {
            tgs_log_info('pos', 'HTSoft sync started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before push order to htsoft
     */
    public static function log_before_push_order_htsoft()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);
            tgs_log_info('pos', 'Order push to HTSoft started', [
                'order_id' => $order_id,
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
