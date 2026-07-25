<?php
/**
 * TGS Error Logger Integration - Purchase Management
 *
 * Tích hợp logging vào tgs_purchase_management
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
     * Hook vào các AJAX handlers
     */
    public static function hook_ajax_handlers()
    {
        if (!class_exists('TGS_Purchase_Order_Ajax')) {
            return;
        }

        // Hook vào save order
        add_action('wp_ajax_tgs_purchase_order_save', [__CLASS__, 'log_before_save_order'], 1);

        // Hook vào update status
        add_action('wp_ajax_tgs_purchase_order_update_status', [__CLASS__, 'log_before_update_status'], 1);

        // Hook vào policy save
        add_action('wp_ajax_tgs_purchase_policy_save', [__CLASS__, 'log_before_save_policy'], 1);

        // Hook vào batch operations
        add_action('wp_ajax_tgs_purchase_batch_save', [__CLASS__, 'log_before_save_batch'], 1);

        // Hook vào payment
        add_action('wp_ajax_tgs_purchase_payment_save', [__CLASS__, 'log_before_save_payment'], 1);
    }

    /**
     * Log before save order
     */
    public static function log_before_save_order()
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
            // Silent fail
        }
    }

    /**
     * Log before update status
     */
    public static function log_before_update_status()
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
            // Silent fail
        }
    }

    /**
     * Log before save policy
     */
    public static function log_before_save_policy()
    {
        try {
            $policy_id = intval($_POST['policy_id'] ?? 0);
            $action = $policy_id > 0 ? 'update' : 'create';

            tgs_log_info('purchase', "Purchase policy {$action} started", [
                'policy_id' => $policy_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent fail
        }
    }

    /**
     * Log before save batch
     */
    public static function log_before_save_batch()
    {
        try {
            $batch_id = intval($_POST['batch_id'] ?? 0);
            $action = $batch_id > 0 ? 'update' : 'create';

            tgs_log_info('purchase', "Purchase batch {$action} started", [
                'batch_id' => $batch_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent fail
        }
    }

    /**
     * Log before save payment
     */
    public static function log_before_save_payment()
    {
        try {
            $payment_id = intval($_POST['payment_id'] ?? 0);
            $action = $payment_id > 0 ? 'update' : 'create';

            tgs_log_info('purchase', "Purchase payment {$action} started", [
                'payment_id' => $payment_id,
                'amount' => floatval($_POST['amount'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent fail
        }
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
