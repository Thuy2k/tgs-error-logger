<?php
/**
 * TGS Error Logger Integration - Selling Policy
 *
 * Tích hợp logging vào tgs_selling_policy
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGS_Error_Logger_Integration_Selling_Policy
{
    /**
     * Init integration
     */
    public static function init()
    {
        // Hook sau khi Selling Policy Ajax class init
        add_action('init', [__CLASS__, 'hook_ajax_handlers'], 999);
    }

    /**
     * Hook vào các AJAX handlers
     */
    public static function hook_ajax_handlers()
    {
        if (!class_exists('TGS_Selling_Policy_Ajax')) {
            return;
        }

        // Hook vào save policy
        add_action('wp_ajax_tgs_selling_policy_save', [__CLASS__, 'log_before_save'], 1);

        // Hook vào delete policy
        add_action('wp_ajax_tgs_selling_policy_delete', [__CLASS__, 'log_before_delete'], 1);

        // Hook vào group save
        add_action('wp_ajax_tgs_selling_policy_group_save', [__CLASS__, 'log_before_group_save'], 1);

        // Hook vào AI operations
        add_action('wp_ajax_tgs_selling_policy_ai_apply_draft', [__CLASS__, 'log_before_ai_apply'], 1);
    }

    /**
     * Log before save policy
     */
    public static function log_before_save()
    {
        // Wrap trong try-catch để không block request
        try {
            $id = intval($_POST['selling_policy_id'] ?? 0);
            $action = $id > 0 ? 'update' : 'create';

            tgs_log_info('selling_policy', "Policy {$action} started", [
                'policy_id' => $id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent fail - không block
        }
    }

    /**
     * Log before delete policy
     */
    public static function log_before_delete()
    {
        try {
            $id = intval($_POST['selling_policy_id'] ?? 0);

            tgs_log_warning('selling_policy', 'Policy deletion started', [
                'policy_id' => $id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent fail
        }
    }

    /**
     * Log before group save
     */
    public static function log_before_group_save()
    {
        try {
            $id = intval($_POST['group_id'] ?? 0);
            $action = $id > 0 ? 'update' : 'create';

            tgs_log_info('selling_policy', "Policy group {$action} started", [
                'group_id' => $id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent fail
        }
    }

    /**
     * Log before AI apply
     */
    public static function log_before_ai_apply()
    {
        try {
            tgs_log_info('selling_policy', 'AI draft apply started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent fail
        }
    }

    /**
     * Wrapper function để log lỗi từ bên trong plugin
     *
     * Gọi từ trong tgs_selling_policy:
     * do_action('tgs_selling_policy_error', $message, $context);
     */
    public static function log_error_from_plugin($message, $context = [])
    {
        tgs_log_selling_policy_error($message, $context);
    }

    /**
     * Wrapper function để log critical error
     *
     * Gọi từ trong tgs_selling_policy:
     * do_action('tgs_selling_policy_critical', $message, $context);
     */
    public static function log_critical_from_plugin($message, $context = [])
    {
        tgs_log_critical('selling_policy', $message, $context);
    }
}

// Hook để plugin có thể gọi
add_action('tgs_selling_policy_error', ['TGS_Error_Logger_Integration_Selling_Policy', 'log_error_from_plugin'], 10, 2);
add_action('tgs_selling_policy_critical', ['TGS_Error_Logger_Integration_Selling_Policy', 'log_critical_from_plugin'], 10, 2);

// Init
TGS_Error_Logger_Integration_Selling_Policy::init();
