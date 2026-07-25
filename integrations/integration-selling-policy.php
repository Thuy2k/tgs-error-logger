<?php
/**
 * TGS Error Logger Integration - Selling Policy
 *
 * Tích hợp logging vào tgs_selling_policy - BẢN ĐẦY ĐỦ
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
        add_action('init', [__CLASS__, 'hook_ajax_handlers'], 999);
    }

    /**
     * Hook vào các AJAX handlers - CHỈ những action thực sự tồn tại
     */
    public static function hook_ajax_handlers()
    {
        if (!class_exists('TGS_Selling_Policy_Ajax')) {
            return;
        }

        // Policy operations
        add_action('wp_ajax_tgs_selling_policy_save', [__CLASS__, 'log_before_save'], 1);
        add_action('wp_ajax_tgs_selling_policy_delete', [__CLASS__, 'log_before_delete'], 1);
        add_action('wp_ajax_tgs_selling_policy_clone', [__CLASS__, 'log_before_clone'], 1);
        add_action('wp_ajax_tgs_selling_policy_get', [__CLASS__, 'log_before_get'], 1);
        add_action('wp_ajax_tgs_selling_policy_list', [__CLASS__, 'log_before_list'], 1);
        add_action('wp_ajax_tgs_selling_policy_next_code', [__CLASS__, 'log_before_next_code'], 1);

        // Group operations
        add_action('wp_ajax_tgs_selling_policy_group_save', [__CLASS__, 'log_before_group_save'], 1);
        add_action('wp_ajax_tgs_selling_policy_group_delete', [__CLASS__, 'log_before_group_delete'], 1);
        add_action('wp_ajax_tgs_selling_policy_group_restore', [__CLASS__, 'log_before_group_restore'], 1);
        add_action('wp_ajax_tgs_selling_policy_group_get', [__CLASS__, 'log_before_group_get'], 1);
        add_action('wp_ajax_tgs_selling_policy_group_list', [__CLASS__, 'log_before_group_list'], 1);
        add_action('wp_ajax_tgs_selling_policy_group_entry_clone', [__CLASS__, 'log_before_group_entry_clone'], 1);
        add_action('wp_ajax_tgs_selling_policy_group_entry_delete', [__CLASS__, 'log_before_group_entry_delete'], 1);

        // Import/Export operations
        add_action('wp_ajax_tgs_selling_policy_group_import', [__CLASS__, 'log_before_group_import'], 1);
        add_action('wp_ajax_tgs_selling_policy_group_import_json', [__CLASS__, 'log_before_group_import_json'], 1);
        add_action('wp_ajax_tgs_selling_policy_group_export', [__CLASS__, 'log_before_group_export'], 1);

        // AI operations
        add_action('wp_ajax_tgs_selling_policy_ai_prepare_draft', [__CLASS__, 'log_before_ai_prepare'], 1);
        add_action('wp_ajax_tgs_selling_policy_ai_apply_draft', [__CLASS__, 'log_before_ai_apply'], 1);
        add_action('wp_ajax_tgs_selling_policy_ai_resolve_skus', [__CLASS__, 'log_before_ai_resolve'], 1);

        // Report operations
        add_action('wp_ajax_tgs_selling_policy_report_list', [__CLASS__, 'log_before_report_list'], 1);
        add_action('wp_ajax_tgs_selling_policy_report_detail', [__CLASS__, 'log_before_report_detail'], 1);

        // Stats
        add_action('wp_ajax_tgs_selling_policy_stats', [__CLASS__, 'log_before_stats'], 1);
    }

    /**
     * Log before save policy
     */
    public static function log_before_save()
    {
        try {
            $id = intval($_POST['selling_policy_id'] ?? 0);
            $action = $id > 0 ? 'update' : 'create';

            tgs_log_info('selling_policy', "Policy {$action} started", [
                'policy_id' => $id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
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
            // Silent
        }
    }

    /**
     * Log before clone policy
     */
    public static function log_before_clone()
    {
        try {
            $id = intval($_POST['selling_policy_id'] ?? 0);

            tgs_log_info('selling_policy', 'Policy clone started', [
                'source_policy_id' => $id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before get policy
     */
    public static function log_before_get()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before list policies
     */
    public static function log_before_list()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before next code
     */
    public static function log_before_next_code()
    {
        // Không log - quá nhiều requests
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
            // Silent
        }
    }

    /**
     * Log before group delete
     */
    public static function log_before_group_delete()
    {
        try {
            $id = intval($_POST['group_id'] ?? 0);

            tgs_log_warning('selling_policy', 'Policy group deletion started', [
                'group_id' => $id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before group restore
     */
    public static function log_before_group_restore()
    {
        try {
            $id = intval($_POST['group_id'] ?? 0);

            tgs_log_info('selling_policy', 'Policy group restore started', [
                'group_id' => $id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before group get
     */
    public static function log_before_group_get()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before group list
     */
    public static function log_before_group_list()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before group entry clone
     */
    public static function log_before_group_entry_clone()
    {
        try {
            $entry_id = intval($_POST['entry_id'] ?? 0);

            tgs_log_info('selling_policy', 'Group entry clone started', [
                'entry_id' => $entry_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before group entry delete
     */
    public static function log_before_group_entry_delete()
    {
        try {
            $entry_id = intval($_POST['entry_id'] ?? 0);

            tgs_log_warning('selling_policy', 'Group entry deletion started', [
                'entry_id' => $entry_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before group import
     */
    public static function log_before_group_import()
    {
        try {
            tgs_log_info('selling_policy', 'Group import started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before group import JSON
     */
    public static function log_before_group_import_json()
    {
        try {
            tgs_log_info('selling_policy', 'Group JSON import started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before group export
     */
    public static function log_before_group_export()
    {
        try {
            $group_id = intval($_POST['group_id'] ?? 0);

            tgs_log_info('selling_policy', 'Group export started', [
                'group_id' => $group_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before AI prepare draft
     */
    public static function log_before_ai_prepare()
    {
        try {
            tgs_log_info('selling_policy', 'AI draft preparation started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before AI apply draft
     */
    public static function log_before_ai_apply()
    {
        try {
            tgs_log_info('selling_policy', 'AI draft apply started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before AI resolve SKUs
     */
    public static function log_before_ai_resolve()
    {
        try {
            tgs_log_info('selling_policy', 'AI SKU resolution started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before report list
     */
    public static function log_before_report_list()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before report detail
     */
    public static function log_before_report_detail()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Log before stats
     */
    public static function log_before_stats()
    {
        // Không log - quá nhiều requests
    }

    /**
     * Wrapper để log từ plugin
     */
    public static function log_error_from_plugin($message, $context = [])
    {
        tgs_log_selling_policy_error($message, $context);
    }

    /**
     * Wrapper để log critical
     */
    public static function log_critical_from_plugin($message, $context = [])
    {
        tgs_log_critical('selling_policy', $message, $context);
    }
}

// Hooks để plugin có thể gọi
add_action('tgs_selling_policy_error', ['TGS_Error_Logger_Integration_Selling_Policy', 'log_error_from_plugin'], 10, 2);
add_action('tgs_selling_policy_critical', ['TGS_Error_Logger_Integration_Selling_Policy', 'log_critical_from_plugin'], 10, 2);

// Init
TGS_Error_Logger_Integration_Selling_Policy::init();
