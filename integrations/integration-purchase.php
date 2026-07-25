<?php
/**
 * TGS Error Logger Integration - Purchase Management
 *
 * Tích hợp logging vào tgs_purchase_management - BẢN ĐẦY ĐỦ
 * 60 AJAX actions coverage
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGS_Error_Logger_Integration_Purchase
{
    /**
     * Init integration
     */
    public static function init()
    {
        add_action('init', [__CLASS__, 'hook_ajax_handlers'], 999);
    }

    /**
     * Hook vào các AJAX handlers - 60 actions
     */
    public static function hook_ajax_handlers()
    {
        if (!class_exists('TGS_Purchase_Order_Ajax')) {
            return;
        }

        // ============ ORDER OPERATIONS (11) ============
        add_action('wp_ajax_tgs_purchase_order_save', [__CLASS__, 'log_before_order_save'], 1);
        add_action('wp_ajax_tgs_purchase_order_get', [__CLASS__, 'log_before_order_get'], 1);
        add_action('wp_ajax_tgs_purchase_order_update_status', [__CLASS__, 'log_before_order_update_status'], 1);
        add_action('wp_ajax_tgs_purchase_order_analysis_lookup', [__CLASS__, 'log_before_order_analysis_lookup'], 1);
        add_action('wp_ajax_tgs_purchase_order_supplier_review', [__CLASS__, 'log_before_order_supplier_review'], 1);
        add_action('wp_ajax_tgs_purchase_order_documents_get', [__CLASS__, 'log_before_order_documents_get'], 1);
        add_action('wp_ajax_tgs_purchase_order_document_upload', [__CLASS__, 'log_before_order_document_upload'], 1);
        add_action('wp_ajax_tgs_purchase_order_document_view', [__CLASS__, 'log_before_order_document_view'], 1);
        add_action('wp_ajax_tgs_purchase_order_document_download', [__CLASS__, 'log_before_order_document_download'], 1);
        add_action('wp_ajax_tgs_purchase_order_check_sku', [__CLASS__, 'log_before_order_check_sku'], 1);
        add_action('wp_ajax_tgs_purchase_order_validate_import_skus', [__CLASS__, 'log_before_order_validate_import_skus'], 1);

        // ============ POLICY OPERATIONS (17) ============
        add_action('wp_ajax_tgs_purchase_policy_list', [__CLASS__, 'log_before_policy_list'], 1);
        add_action('wp_ajax_tgs_purchase_policy_get', [__CLASS__, 'log_before_policy_get'], 1);
        add_action('wp_ajax_tgs_purchase_policy_save', [__CLASS__, 'log_before_policy_save'], 1);
        add_action('wp_ajax_tgs_purchase_policy_delete', [__CLASS__, 'log_before_policy_delete'], 1);
        add_action('wp_ajax_tgs_purchase_policy_clone', [__CLASS__, 'log_before_policy_clone'], 1);

        add_action('wp_ajax_tgs_purchase_policy_item_list', [__CLASS__, 'log_before_policy_item_list'], 1);
        add_action('wp_ajax_tgs_purchase_policy_item_save', [__CLASS__, 'log_before_policy_item_save'], 1);
        add_action('wp_ajax_tgs_purchase_policy_item_delete', [__CLASS__, 'log_before_policy_item_delete'], 1);
        add_action('wp_ajax_tgs_purchase_policy_supplier_products', [__CLASS__, 'log_before_policy_supplier_products'], 1);
        add_action('wp_ajax_tgs_purchase_policy_grid_save', [__CLASS__, 'log_before_policy_grid_save'], 1);
        add_action('wp_ajax_tgs_purchase_policy_item_actions_save', [__CLASS__, 'log_before_policy_item_actions_save'], 1);
        add_action('wp_ajax_tgs_purchase_policy_htsoft_preview', [__CLASS__, 'log_before_policy_htsoft_preview'], 1);
        add_action('wp_ajax_tgs_purchase_policy_htsoft_import', [__CLASS__, 'log_before_policy_htsoft_import'], 1);
        add_action('wp_ajax_tgs_purchase_policy_ai_summary', [__CLASS__, 'log_before_policy_ai_summary'], 1);
        add_action('wp_ajax_tgs_purchase_policy_apply', [__CLASS__, 'log_before_policy_apply'], 1);

        // ============ BATCH OPERATIONS (13) ============
        add_action('wp_ajax_tgs_purchase_batch_list', [__CLASS__, 'log_before_batch_list'], 1);
        add_action('wp_ajax_tgs_purchase_batch_get', [__CLASS__, 'log_before_batch_get'], 1);
        add_action('wp_ajax_tgs_purchase_batch_generate', [__CLASS__, 'log_before_batch_generate'], 1);
        add_action('wp_ajax_tgs_purchase_batch_stats', [__CLASS__, 'log_before_batch_stats'], 1);
        add_action('wp_ajax_tgs_purchase_batch_multi_stats', [__CLASS__, 'log_before_batch_multi_stats'], 1);
        add_action('wp_ajax_tgs_purchase_batch_split', [__CLASS__, 'log_before_batch_split'], 1);
        add_action('wp_ajax_tgs_purchase_batch_merge', [__CLASS__, 'log_before_batch_merge'], 1);
        add_action('wp_ajax_tgs_purchase_batch_confirm', [__CLASS__, 'log_before_batch_confirm'], 1);
        add_action('wp_ajax_tgs_purchase_batch_get_lots_for_assign', [__CLASS__, 'log_before_batch_get_lots_for_assign'], 1);
        add_action('wp_ajax_tgs_purchase_batch_assign_lots', [__CLASS__, 'log_before_batch_assign_lots'], 1);
        add_action('wp_ajax_tgs_purchase_batch_auto_assign_lots', [__CLASS__, 'log_before_batch_auto_assign_lots'], 1);
        add_action('wp_ajax_tgs_purchase_batch_shop_distribution', [__CLASS__, 'log_before_batch_shop_distribution'], 1);

        // ============ PAYMENT OPERATIONS (4) ============
        add_action('wp_ajax_tgs_purchase_payment_list', [__CLASS__, 'log_before_payment_list'], 1);
        add_action('wp_ajax_tgs_purchase_payment_get', [__CLASS__, 'log_before_payment_get'], 1);
        add_action('wp_ajax_tgs_purchase_payment_get_ledgers', [__CLASS__, 'log_before_payment_get_ledgers'], 1);
        add_action('wp_ajax_tgs_purchase_payment_record', [__CLASS__, 'log_before_payment_record'], 1);

        // ============ ALERT OPERATIONS (6) ============
        add_action('wp_ajax_tgs_purchase_alert_summary', [__CLASS__, 'log_before_alert_summary'], 1);
        add_action('wp_ajax_tgs_purchase_alert_shops', [__CLASS__, 'log_before_alert_shops'], 1);
        add_action('wp_ajax_tgs_purchase_alert_shop_stock', [__CLASS__, 'log_before_alert_shop_stock'], 1);
        add_action('wp_ajax_tgs_purchase_alert_batch_low', [__CLASS__, 'log_before_alert_batch_low'], 1);
        add_action('wp_ajax_tgs_purchase_alert_commitment', [__CLASS__, 'log_before_alert_commitment'], 1);
        add_action('wp_ajax_tgs_purchase_alert_finance', [__CLASS__, 'log_before_alert_finance'], 1);

        // ============ ANALYSIS LAYOUT OPERATIONS (4) ============
        add_action('wp_ajax_tgs_purchase_analysis_layout_get', [__CLASS__, 'log_before_analysis_layout_get'], 1);
        add_action('wp_ajax_tgs_purchase_analysis_layout_save', [__CLASS__, 'log_before_analysis_layout_save'], 1);
        add_action('wp_ajax_tgs_purchase_analysis_layout_validate_suppliers', [__CLASS__, 'log_before_analysis_layout_validate_suppliers'], 1);
        add_action('wp_ajax_tgs_purchase_analysis_layout_match_suppliers', [__CLASS__, 'log_before_analysis_layout_match_suppliers'], 1);

        // ============ MISCELLANEOUS (5) ============
        add_action('wp_ajax_tgs_purchase_product_search', [__CLASS__, 'log_before_product_search'], 1);
        add_action('wp_ajax_tgs_purchase_supplier_search', [__CLASS__, 'log_before_supplier_search'], 1);
        add_action('wp_ajax_tgs_purchase_supplier_documents', [__CLASS__, 'log_before_supplier_documents'], 1);
        add_action('wp_ajax_tgs_purchase_upload_file', [__CLASS__, 'log_before_upload_file'], 1);
        add_action('wp_ajax_tgs_purchase_attachment_view', [__CLASS__, 'log_before_attachment_view'], 1);
        add_action('wp_ajax_tgs_purchase_attachment_download', [__CLASS__, 'log_before_attachment_download'], 1);

        // ============ STOCK CONFIG (2) ============
        add_action('wp_ajax_tgs_purchase_order_suggest', [__CLASS__, 'log_before_order_suggest'], 1);
        add_action('wp_ajax_tgs_purchase_ticket_stock_reference', [__CLASS__, 'log_before_ticket_stock_reference'], 1);
    }

    // ========================================
    // ORDER OPERATIONS (11)
    // ========================================

    public static function log_before_order_save()
    {
        try {
            $payload = $_POST['payload'] ?? '';
            $data = is_array($payload) ? $payload : json_decode($payload, true);
            $order_id = $data['id'] ?? 0;
            $action = $order_id > 0 ? 'update' : 'create';

            tgs_log_info('purchase', "Purchase order {$action} started", [
                'order_id' => $order_id,
                'order_code' => $data['order_code'] ?? '',
                'supplier_id' => $data['supplier']['id'] ?? 0,
                'items_count' => count($data['rows'] ?? []),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_order_get()
    {
        // Không log - read operation
    }

    public static function log_before_order_update_status()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);
            $status = sanitize_text_field($_POST['status'] ?? '');

            tgs_log_info('purchase', 'Purchase order status update', [
                'order_id' => $order_id,
                'new_status' => $status,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_order_analysis_lookup()
    {
        // Không log - lookup operation
    }

    public static function log_before_order_supplier_review()
    {
        // Không log - read operation
    }

    public static function log_before_order_documents_get()
    {
        // Không log - read operation
    }

    public static function log_before_order_document_upload()
    {
        try {
            $order_id = intval($_POST['order_id'] ?? 0);

            tgs_log_info('purchase', 'Purchase order document upload', [
                'order_id' => $order_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_order_document_view()
    {
        // Không log - view operation
    }

    public static function log_before_order_document_download()
    {
        // Không log - download operation
    }

    public static function log_before_order_check_sku()
    {
        // Không log - validation operation
    }

    public static function log_before_order_validate_import_skus()
    {
        // Không log - validation operation
    }

    // ========================================
    // POLICY OPERATIONS (17)
    // ========================================

    public static function log_before_policy_list()
    {
        // Không log - list operation
    }

    public static function log_before_policy_get()
    {
        // Không log - read operation
    }

    public static function log_before_policy_save()
    {
        try {
            $policy_id = intval($_POST['policy_id'] ?? 0);
            $action = $policy_id > 0 ? 'update' : 'create';

            tgs_log_info('purchase', "Purchase policy {$action} started", [
                'policy_id' => $policy_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_delete()
    {
        try {
            $policy_id = intval($_POST['policy_id'] ?? 0);

            tgs_log_warning('purchase', 'Purchase policy deletion started', [
                'policy_id' => $policy_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_clone()
    {
        try {
            $policy_id = intval($_POST['policy_id'] ?? 0);

            tgs_log_info('purchase', 'Purchase policy clone started', [
                'source_policy_id' => $policy_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_item_list()
    {
        // Không log - list operation
    }

    public static function log_before_policy_item_save()
    {
        try {
            $item_id = intval($_POST['item_id'] ?? 0);
            $action = $item_id > 0 ? 'update' : 'create';

            tgs_log_info('purchase', "Purchase policy item {$action} started", [
                'item_id' => $item_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_item_delete()
    {
        try {
            $item_id = intval($_POST['item_id'] ?? 0);

            tgs_log_warning('purchase', 'Purchase policy item deletion', [
                'item_id' => $item_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_supplier_products()
    {
        // Không log - read operation
    }

    public static function log_before_policy_grid_save()
    {
        try {
            tgs_log_info('purchase', 'Purchase policy grid save', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_item_actions_save()
    {
        try {
            tgs_log_info('purchase', 'Purchase policy item actions save', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_htsoft_preview()
    {
        // Không log - preview operation
    }

    public static function log_before_policy_htsoft_import()
    {
        try {
            tgs_log_info('purchase', 'Purchase policy HTSoft import started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_ai_summary()
    {
        try {
            tgs_log_info('purchase', 'Purchase policy AI summary started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_policy_apply()
    {
        try {
            $policy_id = intval($_POST['policy_id'] ?? 0);

            tgs_log_info('purchase', 'Purchase policy apply started', [
                'policy_id' => $policy_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    // ========================================
    // BATCH OPERATIONS (13)
    // ========================================

    public static function log_before_batch_list()
    {
        // Không log - list operation
    }

    public static function log_before_batch_get()
    {
        // Không log - read operation
    }

    public static function log_before_batch_generate()
    {
        try {
            tgs_log_info('purchase', 'Purchase batch generate started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_batch_stats()
    {
        // Không log - stats operation
    }

    public static function log_before_batch_multi_stats()
    {
        // Không log - stats operation
    }

    public static function log_before_batch_split()
    {
        try {
            $batch_id = intval($_POST['batch_id'] ?? 0);

            tgs_log_info('purchase', 'Purchase batch split started', [
                'batch_id' => $batch_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_batch_merge()
    {
        try {
            tgs_log_info('purchase', 'Purchase batch merge started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_batch_confirm()
    {
        try {
            tgs_log_info('purchase', 'Purchase batch confirm started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_batch_get_lots_for_assign()
    {
        // Không log - read operation
    }

    public static function log_before_batch_assign_lots()
    {
        try {
            tgs_log_info('purchase', 'Purchase batch assign lots', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_batch_auto_assign_lots()
    {
        try {
            tgs_log_info('purchase', 'Purchase batch auto-assign lots', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_batch_shop_distribution()
    {
        // Không log - read operation
    }

    // ========================================
    // PAYMENT OPERATIONS (4)
    // ========================================

    public static function log_before_payment_list()
    {
        // Không log - list operation
    }

    public static function log_before_payment_get()
    {
        // Không log - read operation
    }

    public static function log_before_payment_get_ledgers()
    {
        // Không log - read operation
    }

    public static function log_before_payment_record()
    {
        try {
            $amount = floatval($_POST['amount'] ?? 0);

            tgs_log_info('purchase', 'Purchase payment record', [
                'amount' => $amount,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    // ========================================
    // ALERT OPERATIONS (6)
    // ========================================

    public static function log_before_alert_summary()
    {
        // Không log - read operation
    }

    public static function log_before_alert_shops()
    {
        // Không log - read operation
    }

    public static function log_before_alert_shop_stock()
    {
        // Không log - read operation
    }

    public static function log_before_alert_batch_low()
    {
        // Không log - read operation
    }

    public static function log_before_alert_commitment()
    {
        // Không log - read operation
    }

    public static function log_before_alert_finance()
    {
        // Không log - read operation
    }

    // ========================================
    // ANALYSIS LAYOUT OPERATIONS (4)
    // ========================================

    public static function log_before_analysis_layout_get()
    {
        // Không log - read operation
    }

    public static function log_before_analysis_layout_save()
    {
        try {
            tgs_log_info('purchase', 'Purchase analysis layout save', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_analysis_layout_validate_suppliers()
    {
        // Không log - validation operation
    }

    public static function log_before_analysis_layout_match_suppliers()
    {
        // Không log - read operation
    }

    // ========================================
    // MISCELLANEOUS (6)
    // ========================================

    public static function log_before_product_search()
    {
        // Không log - search operation
    }

    public static function log_before_supplier_search()
    {
        // Không log - search operation
    }

    public static function log_before_supplier_documents()
    {
        // Không log - read operation
    }

    public static function log_before_upload_file()
    {
        try {
            tgs_log_info('purchase', 'Purchase file upload', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    public static function log_before_attachment_view()
    {
        // Không log - view operation
    }

    public static function log_before_attachment_download()
    {
        // Không log - download operation
    }

    // ========================================
    // STOCK CONFIG (2)
    // ========================================

    public static function log_before_order_suggest()
    {
        // Không log - read operation
    }

    public static function log_before_ticket_stock_reference()
    {
        // Không log - read operation
    }

    /**
     * Wrapper để log từ bên trong plugin
     */
    public static function log_error_from_plugin($message, $context = [])
    {
        tgs_log_purchase_error($message, $context);
    }

    /**
     * Wrapper để log critical
     */
    public static function log_critical_from_plugin($message, $context = [])
    {
        tgs_log_critical('purchase', $message, $context);
    }
}

// Hooks để plugin có thể gọi
add_action('tgs_purchase_error', ['TGS_Error_Logger_Integration_Purchase', 'log_error_from_plugin'], 10, 2);
add_action('tgs_purchase_critical', ['TGS_Error_Logger_Integration_Purchase', 'log_critical_from_plugin'], 10, 2);

// Init
TGS_Error_Logger_Integration_Purchase::init();
