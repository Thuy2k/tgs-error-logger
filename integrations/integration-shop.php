<?php
/**
 * TGS Error Logger Integration - Shop Management
 *
 * Tích hợp logging vào tgs_shop_management
 *
 * @package tgs_error_logger
 */

if (!defined('ABSPATH')) {
    exit;
}

class TGS_Error_Logger_Integration_Shop
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
        // Ticket operations (phiếu mua/bán/hoàn/hủy)
        add_action('wp_ajax_tgs_ticket_datatable', [__CLASS__, 'log_before_ticket_datatable'], 1);
        add_action('wp_ajax_tgs_ticket_approve', [__CLASS__, 'log_before_ticket_approve'], 1);
        add_action('wp_ajax_tgs_ticket_reject', [__CLASS__, 'log_before_ticket_reject'], 1);
        add_action('wp_ajax_tgs_ticket_soft_delete', [__CLASS__, 'log_before_ticket_delete'], 1);

        // Product operations
        add_action('wp_ajax_tgs_product_save', [__CLASS__, 'log_before_product_save'], 1);
        add_action('wp_ajax_tgs_product_delete', [__CLASS__, 'log_before_product_delete'], 1);

        // Category operations
        add_action('wp_ajax_tgs_category_save', [__CLASS__, 'log_before_category_save'], 1);
        add_action('wp_ajax_tgs_category_delete', [__CLASS__, 'log_before_category_delete'], 1);

        // Contact/Customer operations
        add_action('wp_ajax_tgs_contact_save', [__CLASS__, 'log_before_contact_save'], 1);
        add_action('wp_ajax_tgs_contact_delete', [__CLASS__, 'log_before_contact_delete'], 1);

        // Inventory operations
        add_action('wp_ajax_tgs_inventory_manual_save', [__CLASS__, 'log_before_inventory_save'], 1);
        add_action('wp_ajax_tgs_adjustment_save', [__CLASS__, 'log_before_adjustment_save'], 1);

        // Sync operations
        add_action('wp_ajax_tgs_sync_products', [__CLASS__, 'log_before_sync_products'], 1);
        add_action('wp_ajax_tgs_sync_categories', [__CLASS__, 'log_before_sync_categories'], 1);

        // Import operations
        add_action('wp_ajax_tgs_excel_import_products', [__CLASS__, 'log_before_import'], 1);
        add_action('wp_ajax_tgs_excel_import_categories', [__CLASS__, 'log_before_import'], 1);
    }

    /**
     * Log ticket datatable
     */
    public static function log_before_ticket_datatable()
    {
        try {
            $ticket_type = sanitize_text_field($_POST['ticket_type'] ?? '');
            $ledger_type = intval($_POST['ledger_type'] ?? 0);

            // Chỉ log khi có lỗi hoặc query phức tạp
            // Không log mọi request để tránh spam
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before ticket approve
     */
    public static function log_before_ticket_approve()
    {
        try {
            $ledger_id = intval($_POST['ledger_id'] ?? 0);

            tgs_log_info('shop', 'Ticket approve started', [
                'ledger_id' => $ledger_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before ticket reject
     */
    public static function log_before_ticket_reject()
    {
        try {
            $ledger_id = intval($_POST['ledger_id'] ?? 0);

            tgs_log_warning('shop', 'Ticket reject started', [
                'ledger_id' => $ledger_id,
                'reason' => sanitize_text_field($_POST['reason'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before ticket delete
     */
    public static function log_before_ticket_delete()
    {
        try {
            $ledger_id = intval($_POST['ledger_id'] ?? 0);

            tgs_log_warning('shop', 'Ticket soft delete started', [
                'ledger_id' => $ledger_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before product save
     */
    public static function log_before_product_save()
    {
        try {
            $product_id = intval($_POST['product_id'] ?? 0);
            $action = $product_id > 0 ? 'update' : 'create';

            tgs_log_info('shop', "Product {$action} started", [
                'product_id' => $product_id,
                'product_sku' => sanitize_text_field($_POST['product_sku'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before product delete
     */
    public static function log_before_product_delete()
    {
        try {
            $product_id = intval($_POST['product_id'] ?? 0);

            tgs_log_warning('shop', 'Product delete started', [
                'product_id' => $product_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before category save
     */
    public static function log_before_category_save()
    {
        try {
            $cat_id = intval($_POST['cat_id'] ?? 0);
            $action = $cat_id > 0 ? 'update' : 'create';

            tgs_log_info('shop', "Category {$action} started", [
                'cat_id' => $cat_id,
                'cat_name' => sanitize_text_field($_POST['cat_name'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before category delete
     */
    public static function log_before_category_delete()
    {
        try {
            $cat_id = intval($_POST['cat_id'] ?? 0);

            tgs_log_warning('shop', 'Category delete started', [
                'cat_id' => $cat_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before contact save
     */
    public static function log_before_contact_save()
    {
        try {
            $contact_id = intval($_POST['contact_id'] ?? 0);
            $action = $contact_id > 0 ? 'update' : 'create';

            tgs_log_info('shop', "Contact {$action} started", [
                'contact_id' => $contact_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before contact delete
     */
    public static function log_before_contact_delete()
    {
        try {
            $contact_id = intval($_POST['contact_id'] ?? 0);

            tgs_log_warning('shop', 'Contact delete started', [
                'contact_id' => $contact_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before inventory save
     */
    public static function log_before_inventory_save()
    {
        try {
            tgs_log_info('shop', 'Manual inventory adjustment started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before adjustment save
     */
    public static function log_before_adjustment_save()
    {
        try {
            tgs_log_info('shop', 'Stock adjustment started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before sync products
     */
    public static function log_before_sync_products()
    {
        try {
            tgs_log_info('shop', 'Product sync started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before sync categories
     */
    public static function log_before_sync_categories()
    {
        try {
            tgs_log_info('shop', 'Category sync started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before import
     */
    public static function log_before_import()
    {
        try {
            $action = current_action();

            tgs_log_info('shop', 'Excel import started', [
                'action' => $action,
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
        tgs_log_shop_error($message, $context);
    }

    /**
     * Wrapper để log critical
     */
    public static function log_critical_from_plugin($message, $context = [])
    {
        tgs_log_critical('shop', $message, $context);
    }
}

// Hooks để plugin có thể gọi
add_action('tgs_shop_error', ['TGS_Error_Logger_Integration_Shop', 'log_error_from_plugin'], 10, 2);
add_action('tgs_shop_critical', ['TGS_Error_Logger_Integration_Shop', 'log_critical_from_plugin'], 10, 2);

// Init
TGS_Error_Logger_Integration_Shop::init();
