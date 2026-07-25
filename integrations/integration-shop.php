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
        // ============ TICKET OPERATIONS ============
        add_action('wp_ajax_tgs_ticket_datatable', [__CLASS__, 'log_before_ticket_datatable'], 1);
        add_action('wp_ajax_tgs_ticket_approve', [__CLASS__, 'log_before_ticket_approve'], 1);
        add_action('wp_ajax_tgs_ticket_reject', [__CLASS__, 'log_before_ticket_reject'], 1);
        add_action('wp_ajax_tgs_ticket_soft_delete', [__CLASS__, 'log_before_ticket_delete'], 1);
        add_action('wp_ajax_tgs_ticket_restore', [__CLASS__, 'log_before_ticket_restore'], 1);
        add_action('wp_ajax_tgs_ticket_create_transaction', [__CLASS__, 'log_before_create_transaction'], 1);
        add_action('wp_ajax_tgs_ticket_update_note', [__CLASS__, 'log_before_update_note'], 1);

        // ============ PRODUCT OPERATIONS ============
        add_action('wp_ajax_tgs_shop_product_create', [__CLASS__, 'log_before_product_create'], 1);
        add_action('wp_ajax_tgs_shop_product_update', [__CLASS__, 'log_before_product_update'], 1);
        add_action('wp_ajax_tgs_shop_product_delete', [__CLASS__, 'log_before_product_delete'], 1);
        add_action('wp_ajax_tgs_shop_product_quick_update_price', [__CLASS__, 'log_before_product_quick_update_price'], 1);
        add_action('wp_ajax_tgs_shop_product_quick_update_status', [__CLASS__, 'log_before_product_quick_update_status'], 1);
        add_action('wp_ajax_tgs_shop_product_quick_update_quantity', [__CLASS__, 'log_before_product_quick_update_quantity'], 1);
        add_action('wp_ajax_tgs_shop_product_bulk_update_tracking', [__CLASS__, 'log_before_product_bulk_update_tracking'], 1);
        add_action('wp_ajax_tgs_shop_product_bulk_update_all_tracking', [__CLASS__, 'log_before_product_bulk_update_all_tracking'], 1);

        // ============ CATEGORY OPERATIONS ============
        add_action('wp_ajax_tgs_category_save', [__CLASS__, 'log_before_category_save'], 1);
        add_action('wp_ajax_tgs_category_delete', [__CLASS__, 'log_before_category_delete'], 1);
        add_action('wp_ajax_tgs_global_category_create', [__CLASS__, 'log_before_global_category_create'], 1);
        add_action('wp_ajax_tgs_global_category_update', [__CLASS__, 'log_before_global_category_update'], 1);
        add_action('wp_ajax_tgs_global_category_delete', [__CLASS__, 'log_before_global_category_delete'], 1);

        // ============ CONTACT/CUSTOMER OPERATIONS ============
        add_action('wp_ajax_tgs_contact_save', [__CLASS__, 'log_before_contact_save'], 1);
        add_action('wp_ajax_tgs_contact_delete', [__CLASS__, 'log_before_contact_delete'], 1);
        add_action('wp_ajax_tgs_customer_create', [__CLASS__, 'log_before_customer_create'], 1);
        add_action('wp_ajax_tgs_customer_update', [__CLASS__, 'log_before_customer_update'], 1);
        add_action('wp_ajax_tgs_customer_delete', [__CLASS__, 'log_before_customer_delete'], 1);
        add_action('wp_ajax_tgs_merge_guest_to_customer', [__CLASS__, 'log_before_merge_guest'], 1);

        // ============ INVENTORY OPERATIONS ============
        add_action('wp_ajax_tgs_inventory_manual_save', [__CLASS__, 'log_before_inventory_save'], 1);
        add_action('wp_ajax_tgs_adjustment_save', [__CLASS__, 'log_before_adjustment_save'], 1);
        add_action('wp_ajax_tgs_shop_inventory_update_lots_exp', [__CLASS__, 'log_before_inventory_update_lots_exp'], 1);

        // ============ SYNC OPERATIONS ============
        add_action('wp_ajax_tgs_shop_sync_products', [__CLASS__, 'log_before_sync_products'], 1);
        add_action('wp_ajax_tgs_shop_sync_categories', [__CLASS__, 'log_before_sync_categories'], 1);
        add_action('wp_ajax_tgs_shop_sync_selected_products', [__CLASS__, 'log_before_sync_selected_products'], 1);
        add_action('wp_ajax_tgs_shop_sync_selected_categories', [__CLASS__, 'log_before_sync_selected_categories'], 1);

        // ============ IMPORT/EXPORT OPERATIONS ============
        add_action('wp_ajax_tgs_excel_import_products', [__CLASS__, 'log_before_import_products'], 1);
        add_action('wp_ajax_tgs_excel_import_categories', [__CLASS__, 'log_before_import_categories'], 1);
        add_action('wp_ajax_tgs_excel_import_product', [__CLASS__, 'log_before_import_single_product'], 1);
        add_action('wp_ajax_tgs_ticket_excel_bulk_create_products', [__CLASS__, 'log_before_bulk_create_products'], 1);
        add_action('wp_ajax_tgs_shop_inventory_export', [__CLASS__, 'log_before_inventory_export'], 1);
        add_action('wp_ajax_tgs_shop_ledger_export', [__CLASS__, 'log_before_ledger_export'], 1);

        // ============ SUPPLIER OPERATIONS ============
        add_action('wp_ajax_tgs_shop_add_supplier', [__CLASS__, 'log_before_add_supplier'], 1);
        add_action('wp_ajax_tgs_shop_update_supplier', [__CLASS__, 'log_before_update_supplier'], 1);
        add_action('wp_ajax_tgs_shop_delete_supplier', [__CLASS__, 'log_before_delete_supplier'], 1);
        add_action('wp_ajax_tgs_global_supplier_create', [__CLASS__, 'log_before_global_supplier_create'], 1);
        add_action('wp_ajax_tgs_global_supplier_update', [__CLASS__, 'log_before_global_supplier_update'], 1);
        add_action('wp_ajax_tgs_global_supplier_delete', [__CLASS__, 'log_before_global_supplier_delete'], 1);

        // ============ LOT TRACKING OPERATIONS ============
        add_action('wp_ajax_tgs_lot_create', [__CLASS__, 'log_before_lot_create'], 1);
        add_action('wp_ajax_tgs_lot_update', [__CLASS__, 'log_before_lot_update'], 1);
        add_action('wp_ajax_tgs_lot_delete', [__CLASS__, 'log_before_lot_delete'], 1);
        add_action('wp_ajax_tgs_lot_bulk_update_status', [__CLASS__, 'log_before_lot_bulk_update'], 1);

        // ============ IDENTIFIER OPERATIONS ============
        add_action('wp_ajax_tgs_identifier_create_ticket', [__CLASS__, 'log_before_identifier_create'], 1);
        add_action('wp_ajax_tgs_identifier_update_ticket', [__CLASS__, 'log_before_identifier_update'], 1);
        add_action('wp_ajax_tgs_identifier_activate_lots', [__CLASS__, 'log_before_identifier_activate'], 1);

        // ============ TRANSACTION OPERATIONS ============
        add_action('wp_ajax_tgs_create_transaction_from_pending', [__CLASS__, 'log_before_create_transaction_from_pending'], 1);
        add_action('wp_ajax_tgs_quick_approve_transaction', [__CLASS__, 'log_before_quick_approve_transaction'], 1);
        add_action('wp_ajax_tgs_quick_reject_transaction', [__CLASS__, 'log_before_quick_reject_transaction'], 1);

        // ============ WAREHOUSE ZONE OPERATIONS ============
        add_action('wp_ajax_tgs_warehouse_zone_create', [__CLASS__, 'log_before_warehouse_zone_create'], 1);
        add_action('wp_ajax_tgs_warehouse_zone_update', [__CLASS__, 'log_before_warehouse_zone_update'], 1);
        add_action('wp_ajax_tgs_warehouse_zone_delete', [__CLASS__, 'log_before_warehouse_zone_delete'], 1);
        add_action('wp_ajax_tgs_warehouse_zone_bulk_create', [__CLASS__, 'log_before_warehouse_zone_bulk_create'], 1);

        // ============ SETTINGS OPERATIONS ============
        add_action('wp_ajax_tgs_shop_save_print_settings', [__CLASS__, 'log_before_save_print_settings'], 1);
        add_action('wp_ajax_tgs_shop_save_label_print_settings', [__CLASS__, 'log_before_save_label_print_settings'], 1);
        add_action('wp_ajax_tgs_save_brand_settings', [__CLASS__, 'log_before_save_brand_settings'], 1);

        // ============ MILK UNDER 24M OPERATIONS ============
        add_action('wp_ajax_tgs_milk_under24m_import_single', [__CLASS__, 'log_before_milk_import_single'], 1);
        add_action('wp_ajax_tgs_milk_under24m_batch_import', [__CLASS__, 'log_before_milk_batch_import'], 1);
        add_action('wp_ajax_tgs_milk_under24m_delete', [__CLASS__, 'log_before_milk_delete'], 1);
        add_action('wp_ajax_tgs_milk_under24m_bulk_delete', [__CLASS__, 'log_before_milk_bulk_delete'], 1);
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
     * Log before ticket restore
     */
    public static function log_before_ticket_restore()
    {
        try {
            $ledger_id = intval($_POST['ledger_id'] ?? 0);

            tgs_log_info('shop', 'Ticket restore started', [
                'ledger_id' => $ledger_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before create transaction
     */
    public static function log_before_create_transaction()
    {
        try {
            $ledger_id = intval($_POST['ledger_id'] ?? 0);

            tgs_log_info('shop', 'Transaction creation started', [
                'ledger_id' => $ledger_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before update note
     */
    public static function log_before_update_note()
    {
        try {
            $ledger_id = intval($_POST['ledger_id'] ?? 0);

            tgs_log_info('shop', 'Ticket note update started', [
                'ledger_id' => $ledger_id,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before product create
     */
    public static function log_before_product_create()
    {
        try {
            tgs_log_info('shop', 'Product create started', [
                'product_sku' => sanitize_text_field($_POST['product_sku'] ?? ''),
                'product_name' => sanitize_text_field($_POST['product_name'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before product update
     */
    public static function log_before_product_update()
    {
        try {
            $product_id = intval($_POST['product_id'] ?? 0);

            tgs_log_info('shop', 'Product update started', [
                'product_id' => $product_id,
                'product_sku' => sanitize_text_field($_POST['product_sku'] ?? ''),
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
     * Log before product quick update price
     */
    public static function log_before_product_quick_update_price()
    {
        try {
            tgs_log_info('shop', 'Product quick price update started', [
                'product_id' => intval($_POST['product_id'] ?? 0),
                'new_price' => floatval($_POST['price'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before product quick update status
     */
    public static function log_before_product_quick_update_status()
    {
        try {
            tgs_log_info('shop', 'Product quick status update started', [
                'product_id' => intval($_POST['product_id'] ?? 0),
                'new_status' => sanitize_text_field($_POST['status'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before product quick update quantity
     */
    public static function log_before_product_quick_update_quantity()
    {
        try {
            tgs_log_info('shop', 'Product quick quantity update started', [
                'product_id' => intval($_POST['product_id'] ?? 0),
                'new_quantity' => intval($_POST['quantity'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before product bulk update tracking
     */
    public static function log_before_product_bulk_update_tracking()
    {
        try {
            $product_ids = isset($_POST['product_ids']) ? json_decode(stripslashes($_POST['product_ids']), true) : [];

            tgs_log_info('shop', 'Product bulk tracking update started', [
                'product_count' => count($product_ids),
                'enable_tracking' => isset($_POST['enable_tracking']) ? boolval($_POST['enable_tracking']) : null,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before product bulk update all tracking
     */
    public static function log_before_product_bulk_update_all_tracking()
    {
        try {
            tgs_log_warning('shop', 'Bulk update ALL products tracking started', [
                'enable_tracking' => isset($_POST['enable_tracking']) ? boolval($_POST['enable_tracking']) : null,
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before global category create
     */
    public static function log_before_global_category_create()
    {
        try {
            tgs_log_info('shop', 'Global category create started', [
                'cat_name' => sanitize_text_field($_POST['cat_name'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before global category update
     */
    public static function log_before_global_category_update()
    {
        try {
            tgs_log_info('shop', 'Global category update started', [
                'cat_id' => intval($_POST['cat_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before global category delete
     */
    public static function log_before_global_category_delete()
    {
        try {
            tgs_log_warning('shop', 'Global category delete started', [
                'cat_id' => intval($_POST['cat_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before customer create
     */
    public static function log_before_customer_create()
    {
        try {
            tgs_log_info('shop', 'Customer create started', [
                'customer_name' => sanitize_text_field($_POST['customer_name'] ?? ''),
                'customer_phone' => sanitize_text_field($_POST['customer_phone'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before customer update
     */
    public static function log_before_customer_update()
    {
        try {
            tgs_log_info('shop', 'Customer update started', [
                'customer_id' => intval($_POST['customer_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before customer delete
     */
    public static function log_before_customer_delete()
    {
        try {
            tgs_log_warning('shop', 'Customer delete started', [
                'customer_id' => intval($_POST['customer_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before merge guest
     */
    public static function log_before_merge_guest()
    {
        try {
            tgs_log_info('shop', 'Merge guest to customer started', [
                'guest_id' => intval($_POST['guest_id'] ?? 0),
                'customer_id' => intval($_POST['customer_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before inventory update lots expiry
     */
    public static function log_before_inventory_update_lots_exp()
    {
        try {
            tgs_log_info('shop', 'Inventory lots expiry update started', [
                'lot_ids' => isset($_POST['lot_ids']) ? json_decode(stripslashes($_POST['lot_ids']), true) : [],
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before sync selected products
     */
    public static function log_before_sync_selected_products()
    {
        try {
            $product_ids = isset($_POST['product_ids']) ? json_decode(stripslashes($_POST['product_ids']), true) : [];

            tgs_log_info('shop', 'Selected products sync started', [
                'product_count' => count($product_ids),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before sync selected categories
     */
    public static function log_before_sync_selected_categories()
    {
        try {
            $cat_ids = isset($_POST['cat_ids']) ? json_decode(stripslashes($_POST['cat_ids']), true) : [];

            tgs_log_info('shop', 'Selected categories sync started', [
                'category_count' => count($cat_ids),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before import products
     */
    public static function log_before_import_products()
    {
        try {
            tgs_log_info('shop', 'Excel products import started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before import categories
     */
    public static function log_before_import_categories()
    {
        try {
            tgs_log_info('shop', 'Excel categories import started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before import single product
     */
    public static function log_before_import_single_product()
    {
        try {
            tgs_log_info('shop', 'Single product import started', [
                'product_sku' => sanitize_text_field($_POST['product_sku'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before bulk create products
     */
    public static function log_before_bulk_create_products()
    {
        try {
            $products = isset($_POST['products']) ? json_decode(stripslashes($_POST['products']), true) : [];

            tgs_log_info('shop', 'Bulk products create started', [
                'product_count' => count($products),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before inventory export
     */
    public static function log_before_inventory_export()
    {
        try {
            tgs_log_info('shop', 'Inventory export started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before ledger export
     */
    public static function log_before_ledger_export()
    {
        try {
            tgs_log_info('shop', 'Ledger export started', [
                'ledger_type' => intval($_POST['ledger_type'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before add supplier
     */
    public static function log_before_add_supplier()
    {
        try {
            tgs_log_info('shop', 'Supplier add started', [
                'supplier_name' => sanitize_text_field($_POST['supplier_name'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before update supplier
     */
    public static function log_before_update_supplier()
    {
        try {
            tgs_log_info('shop', 'Supplier update started', [
                'supplier_id' => intval($_POST['supplier_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before delete supplier
     */
    public static function log_before_delete_supplier()
    {
        try {
            tgs_log_warning('shop', 'Supplier delete started', [
                'supplier_id' => intval($_POST['supplier_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before global supplier create
     */
    public static function log_before_global_supplier_create()
    {
        try {
            tgs_log_info('shop', 'Global supplier create started', [
                'supplier_name' => sanitize_text_field($_POST['supplier_name'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before global supplier update
     */
    public static function log_before_global_supplier_update()
    {
        try {
            tgs_log_info('shop', 'Global supplier update started', [
                'supplier_id' => intval($_POST['supplier_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before global supplier delete
     */
    public static function log_before_global_supplier_delete()
    {
        try {
            tgs_log_warning('shop', 'Global supplier delete started', [
                'supplier_id' => intval($_POST['supplier_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before lot create
     */
    public static function log_before_lot_create()
    {
        try {
            tgs_log_info('shop', 'Product lot create started', [
                'product_id' => intval($_POST['product_id'] ?? 0),
                'lot_code' => sanitize_text_field($_POST['lot_code'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before lot update
     */
    public static function log_before_lot_update()
    {
        try {
            tgs_log_info('shop', 'Product lot update started', [
                'lot_id' => intval($_POST['lot_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before lot delete
     */
    public static function log_before_lot_delete()
    {
        try {
            tgs_log_warning('shop', 'Product lot delete started', [
                'lot_id' => intval($_POST['lot_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before lot bulk update
     */
    public static function log_before_lot_bulk_update()
    {
        try {
            $lot_ids = isset($_POST['lot_ids']) ? json_decode(stripslashes($_POST['lot_ids']), true) : [];

            tgs_log_info('shop', 'Product lots bulk status update started', [
                'lot_count' => count($lot_ids),
                'new_status' => sanitize_text_field($_POST['status'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before identifier create
     */
    public static function log_before_identifier_create()
    {
        try {
            tgs_log_info('shop', 'Identifier ticket create started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before identifier update
     */
    public static function log_before_identifier_update()
    {
        try {
            tgs_log_info('shop', 'Identifier ticket update started', [
                'ledger_id' => intval($_POST['ledger_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before identifier activate
     */
    public static function log_before_identifier_activate()
    {
        try {
            $lot_ids = isset($_POST['lot_ids']) ? json_decode(stripslashes($_POST['lot_ids']), true) : [];

            tgs_log_info('shop', 'Identifier lots activate started', [
                'lot_count' => count($lot_ids),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before create transaction from pending
     */
    public static function log_before_create_transaction_from_pending()
    {
        try {
            tgs_log_info('shop', 'Transaction from pending started', [
                'pending_ids' => isset($_POST['pending_ids']) ? json_decode(stripslashes($_POST['pending_ids']), true) : [],
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before quick approve transaction
     */
    public static function log_before_quick_approve_transaction()
    {
        try {
            tgs_log_info('shop', 'Quick approve transaction started', [
                'ledger_id' => intval($_POST['ledger_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before quick reject transaction
     */
    public static function log_before_quick_reject_transaction()
    {
        try {
            tgs_log_warning('shop', 'Quick reject transaction started', [
                'ledger_id' => intval($_POST['ledger_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before warehouse zone create
     */
    public static function log_before_warehouse_zone_create()
    {
        try {
            tgs_log_info('shop', 'Warehouse zone create started', [
                'zone_name' => sanitize_text_field($_POST['zone_name'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before warehouse zone update
     */
    public static function log_before_warehouse_zone_update()
    {
        try {
            tgs_log_info('shop', 'Warehouse zone update started', [
                'zone_id' => intval($_POST['zone_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before warehouse zone delete
     */
    public static function log_before_warehouse_zone_delete()
    {
        try {
            tgs_log_warning('shop', 'Warehouse zone delete started', [
                'zone_id' => intval($_POST['zone_id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before warehouse zone bulk create
     */
    public static function log_before_warehouse_zone_bulk_create()
    {
        try {
            $zones = isset($_POST['zones']) ? json_decode(stripslashes($_POST['zones']), true) : [];

            tgs_log_info('shop', 'Warehouse zones bulk create started', [
                'zone_count' => count($zones),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save print settings
     */
    public static function log_before_save_print_settings()
    {
        try {
            tgs_log_info('shop', 'Print settings save started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save label print settings
     */
    public static function log_before_save_label_print_settings()
    {
        try {
            tgs_log_info('shop', 'Label print settings save started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before save brand settings
     */
    public static function log_before_save_brand_settings()
    {
        try {
            tgs_log_info('shop', 'Brand settings save started', [
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before milk import single
     */
    public static function log_before_milk_import_single()
    {
        try {
            tgs_log_info('shop', 'Milk under 24m single import started', [
                'product_sku' => sanitize_text_field($_POST['product_sku'] ?? ''),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before milk batch import
     */
    public static function log_before_milk_batch_import()
    {
        try {
            $products = isset($_POST['products']) ? json_decode(stripslashes($_POST['products']), true) : [];

            tgs_log_info('shop', 'Milk under 24m batch import started', [
                'product_count' => count($products),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before milk delete
     */
    public static function log_before_milk_delete()
    {
        try {
            tgs_log_warning('shop', 'Milk under 24m delete started', [
                'record_id' => intval($_POST['id'] ?? 0),
                'user_id' => get_current_user_id(),
            ]);
        } catch (Exception $e) {
            // Silent
        }
    }

    /**
     * Log before milk bulk delete
     */
    public static function log_before_milk_bulk_delete()
    {
        try {
            $ids = isset($_POST['ids']) ? json_decode(stripslashes($_POST['ids']), true) : [];

            tgs_log_warning('shop', 'Milk under 24m bulk delete started', [
                'record_count' => count($ids),
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
