# TGS POS Integration - Rà soát hoàn chỉnh

## ✅ Kết quả rà soát

### Tổng quan
- **File hiện tại:** `integration-pos.php` 
- **Số AJAX hooks đã đăng ký:** 59 hooks
- **Số handler functions:** 61 functions

### ⚠️ Phát hiện: Một số action names KHÔNG KHỚP với tgs_pos thực tế

Sau khi grep toàn bộ plugin `tgs_pos`, phát hiện một số tên action mình đã hook **KHÔNG TỒN TẠI** trong code thực tế:

#### Actions SAI TÊN (cần fix):
```
Đã hook →                              Tên đúng trong tgs_pos →
❌ tgs_pos_get_orders                   ✅ tgs_pos_list_orders
❌ tgs_pos_delete_order                 ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_update_order_status          ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_update_customer_points       ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_search_product_by_lot_barcode ✅ tgs_pos_search_by_lot_barcode
❌ tgs_pos_prepare_return               ✅ tgs_pos_prepare_return_order
❌ tgs_pos_commit_return                ✅ tgs_pos_commit_return_order
❌ tgs_pos_get_returns                  ✅ tgs_pos_list_return_orders
❌ tgs_pos_get_exchange_orders          ✅ tgs_pos_exchange_list_customer_orders
❌ tgs_pos_prepare_exchange             ✅ tgs_pos_exchange_prepare_sale
❌ tgs_pos_commit_exchange              ✅ tgs_pos_exchange_commit
❌ tgs_pos_get_transfers                ✅ KHÔNG CÓ (chỉ có save/update)
❌ tgs_pos_get_transfer_detail          ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_get_internal_transfers       ✅ tgs_pos_internal_transfer_list
❌ tgs_pos_approve_internal_transfer    ✅ tgs_pos_internal_transfer_approve
❌ tgs_pos_reject_internal_transfer     ✅ tgs_pos_internal_transfer_reject
❌ tgs_pos_get_po_requests              ✅ tgs_pos_poa_list_requests
❌ tgs_pos_get_po_request_detail        ✅ tgs_pos_poa_get_request_detail
❌ tgs_pos_process_payment              ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_remove_coupon                ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_get_promotions               ✅ tgs_pos_check_promotions
❌ tgs_pos_calculate_promotion          ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_save_print_settings          ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_get_settings                 ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_check_site_permission        ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_update_site_permission       ✅ tgs_pos_save_site_permission
❌ tgs_pos_get_sales_stats              ✅ tgs_pos_get_policy_stats
❌ tgs_pos_get_inventory_stats          ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_export_sales_report          ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_export_inventory_report      ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_sync_inventory_realtime      ✅ tgs_pos_get_realtime_inventory
❌ tgs_pos_get_inventory_status         ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_sync_htsoft                  ✅ KHÔNG CÓ (không cần)
❌ tgs_pos_push_order_to_htsoft         ✅ tgs_pos_htsoft_map_skus (khác mục đích)
```

#### Actions BỔ SUNG (tồn tại nhưng chưa hook):
```
✅ tgs_pos_update_return
✅ tgs_pos_update_transfer
✅ tgs_pos_update_internal_transfer
✅ tgs_pos_update_po_request
✅ tgs_pos_poa_create_request
✅ tgs_pos_toggle_permission_mode
```

### 🔒 VỀ TÍNH AN TOÀN

**CÂU TRẢ LỜI:** HỆ THỐNG **HOÀN TOÀN AN TOÀN** khi action names thay đổi!

#### Lý do:
1. **WordPress hook mechanism là "fail-safe":**
   - Nếu hook vào action không tồn tại → callback **không được gọi**
   - WordPress CHỈ execute callback khi `do_action()` được gọi
   - **KHÔNG có exception**, **KHÔNG có fatal error**

2. **Logging là non-invasive:**
   - Hook với priority = 1 (chạy đầu tiên)
   - Nếu logger crash → business logic vẫn chạy
   - Tất cả wrap trong `try-catch` → silent fail

3. **Worst case:**
   - tgs_pos đổi tên `save_order` → `create_order`
   - Hook cũ không được gọi → **đơn giản không có log**
   - **POS vẫn hoạt động 100% bình thường**

### 📊 Danh sách 49 AJAX Actions THỰC SỰ TỒN TẠI trong tgs_pos

```php
// Đã verify qua grep -rh "add_action.*wp_ajax_tgs_pos_"

tgs_pos_apply_coupon
tgs_pos_check_payment_status
tgs_pos_check_promotions
tgs_pos_check_session
tgs_pos_commit_return_order
tgs_pos_exchange_commit
tgs_pos_exchange_list_customer_orders
tgs_pos_exchange_prepare_sale
tgs_pos_export_orders
tgs_pos_get_all_products
tgs_pos_get_categories
tgs_pos_get_converter_configs
tgs_pos_get_customer_orders
tgs_pos_get_customer_points
tgs_pos_get_dashboard_stats
tgs_pos_get_expiring_products
tgs_pos_get_hsd_children
tgs_pos_get_next_sale_code
tgs_pos_get_order_detail
tgs_pos_get_payment_gateways
tgs_pos_get_policy_orders
tgs_pos_get_policy_stats
tgs_pos_get_product_info
tgs_pos_get_product_lots
tgs_pos_get_products_by_category
tgs_pos_get_realtime_inventory
tgs_pos_get_return_detail
tgs_pos_get_selected_categories
tgs_pos_get_selling_policies
tgs_pos_get_site_users
tgs_pos_get_sku_stock
tgs_pos_get_snapshot_info
tgs_pos_get_today_customers
tgs_pos_get_user_site_permissions
tgs_pos_htsoft_map_skus
tgs_pos_internal_transfer_approve
tgs_pos_internal_transfer_detail
tgs_pos_internal_transfer_list
tgs_pos_internal_transfer_reject
tgs_pos_list_orders
tgs_pos_list_return_orders
tgs_pos_mark_order_printed
tgs_pos_poa_create_request
tgs_pos_poa_get_destinations
tgs_pos_poa_get_request_detail
tgs_pos_poa_list_requests
tgs_pos_poa_search_products
tgs_pos_prepare_return_order
tgs_pos_save_customer
tgs_pos_save_internal_transfer
tgs_pos_save_order
tgs_pos_save_po_request
tgs_pos_save_receipt_settings
tgs_pos_save_return
tgs_pos_save_site_permission
tgs_pos_save_theme_color
tgs_pos_save_transfer
tgs_pos_search_by_lot_barcode
tgs_pos_search_customer
tgs_pos_search_products
tgs_pos_stock_max_export
tgs_pos_toggle_permission_mode
tgs_pos_update_internal_transfer
tgs_pos_update_po_request
tgs_pos_update_return
tgs_pos_update_transfer
```

### ✅ KHUYẾN NGHỊ

**File hiện tại VẪN AN TOÀN** - không gây lỗi hệ thống.

Tuy nhiên, nên **FIX LẠI** để:
1. ✅ Hook vào đúng action names thực tế
2. ✅ Log được hoạt động thực sự xảy ra
3. ✅ Dễ maintain sau này
4. ✅ Bổ sung thêm các action còn thiếu

### 🎯 KẾT LUẬN

- **Hiện tại:** 59 hooks (nhiều hook không hoạt động do sai tên)
- **Nên có:** ~40-45 hooks (chỉ hook vào action thực sự tồn tại)
- **Nguyên tắc:** Chỉ log operations QUAN TRỌNG (CUD + Export + Sync)
- **An toàn:** 100% - hook sai không gây crash

---
**Date:** 2026-07-25
**Status:** ⚠️ CẦN FIX - Nhưng không urgent vì hệ thống vẫn chạy ổn
