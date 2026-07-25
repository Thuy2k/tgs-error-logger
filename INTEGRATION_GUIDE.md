# TGS Error Logger - Integration Guide

## Tích hợp đã hoàn thành

Plugin TGS Error Logger đã được tích hợp sẵn vào 4 plugin quan trọng:

### ✅ 1. tgs_pos (POS System)
**Module**: `pos`  
**File integration**: `integrations/integration-pos.php`

**Logs tự động:**
- ✅ Tạo/cập nhật đơn hàng
- ✅ In hóa đơn
- ✅ Tạo khách hàng
- ✅ Hoàn hàng
- ✅ Điều chuyển hàng
- ✅ Yêu cầu nhập hàng
- ✅ Xuất báo cáo
- ✅ Cập nhật cài đặt

**Cách log thủ công từ trong plugin:**
```php
// Option 1: Dùng action hook
do_action('tgs_pos_error', 'Order save failed', [
    'order_id' => $order_id,
    'reason' => $error_message
]);

// Option 2: Dùng helper function
if (function_exists('tgs_log_pos_error')) {
    tgs_log_pos_error('Order save failed', [
        'order_id' => $order_id
    ]);
}

// Critical error
do_action('tgs_pos_critical', 'Database connection lost', []);
```

---

### ✅ 2. tgs_shop_management (Quản lý cửa hàng)
**Module**: `shop`  
**File integration**: `integrations/integration-shop.php`

**Logs tự động:**
- ✅ Phê duyệt/từ chối phiếu (purchase/sale/return/damage)
- ✅ Xóa phiếu
- ✅ Tạo/cập nhật/xóa sản phẩm
- ✅ Tạo/cập nhật/xóa danh mục
- ✅ Tạo/cập nhật/xóa khách hàng/nhà cung cấp
- ✅ Điều chỉnh tồn kho
- ✅ Đồng bộ dữ liệu
- ✅ Import Excel

**Cách log thủ công:**
```php
// Option 1: Action hook
do_action('tgs_shop_error', 'Product sync failed', [
    'product_id' => $product_id,
    'sync_type' => 'full'
]);

// Option 2: Helper function
if (function_exists('tgs_log_shop_error')) {
    tgs_log_shop_error('Category save failed', [
        'cat_id' => $cat_id
    ]);
}

// Critical
do_action('tgs_shop_critical', 'Inventory calculation error', [
    'product_count' => $count
]);
```

---

### ✅ 3. tgs_selling_policy (Chính sách bán hàng)
**Module**: `selling_policy`  
**File integration**: `integrations/integration-selling-policy.php`

**Logs tự động:**
- ✅ Tạo/cập nhật chính sách
- ✅ Xóa chính sách
- ✅ Tạo/cập nhật nhóm chính sách
- ✅ AI apply draft

**Cách log thủ công:**
```php
// Option 1: Action hook
do_action('tgs_selling_policy_error', 'Policy validation failed', [
    'policy_id' => $policy_id,
    'validation_errors' => $errors
]);

// Option 2: Helper function
if (function_exists('tgs_log_selling_policy_error')) {
    tgs_log_selling_policy_error('Policy engine error', [
        'policy_id' => $policy_id
    ]);
}

// Critical
do_action('tgs_selling_policy_critical', 'Policy calculation error', []);
```

---

### ✅ 4. tgs_purchase_management (Quản lý mua hàng)
**Module**: `purchase`  
**File integration**: `integrations/integration-purchase.php`

**Logs tự động:**
- ✅ Tạo/cập nhật đơn mua hàng
- ✅ Cập nhật trạng thái đơn
- ✅ Tạo/cập nhật chính sách mua
- ✅ Tạo/cập nhật batch
- ✅ Tạo/cập nhật thanh toán

**Cách log thủ công:**
```php
// Option 1: Action hook
do_action('tgs_purchase_error', 'Purchase order validation failed', [
    'order_id' => $order_id,
    'supplier_id' => $supplier_id
]);

// Option 2: Helper function
if (function_exists('tgs_log_purchase_error')) {
    tgs_log_purchase_error('Order save failed', [
        'order_id' => $order_id
    ]);
}

// Critical
do_action('tgs_purchase_critical', 'Supplier sync failed', []);
```

---

## Cấu trúc logs

Logs được lưu theo cấu trúc:
```
wp-content/uploads/sites/{blog_id}/tgs_logs/
├── pos/
│   ├── 2026-07-25.jsonl
│   └── ...
├── shop/
│   ├── 2026-07-25.jsonl
│   └── ...
├── selling_policy/
│   ├── 2026-07-25.jsonl
│   └── ...
├── purchase/
│   ├── 2026-07-25.jsonl
│   └── ...
└── security/
    └── 2026-07-25.jsonl
```

## Xem logs

### 1. Qua UI Admin
- Vào menu **"Nhật ký lỗi"** trong WordPress Admin
- Chọn cửa hàng (blog_id)
- Chọn module (pos, shop, selling_policy, purchase)
- Chọn ngày
- Filter theo level, search

### 2. Qua code
```php
$reader = TGS_Error_Logger_Reader::instance();

// Đọc logs của module POS
$logs = $reader->read_logs(
    get_current_blog_id(),
    'pos',
    '2026-07-25'
);

// Lấy thống kê
$stats = $reader->get_summary_stats(
    get_current_blog_id(),
    'selling_policy',
    '2026-07-25'
);

// Lấy security issues
$issues = $reader->get_security_issues(
    get_current_blog_id(),
    7 // 7 days
);
```

## Log Levels

| Level | Khi nào dùng | Example |
|-------|--------------|---------|
| `critical` | Hệ thống ngừng hoạt động, mất dữ liệu | Database connection lost, Fatal error |
| `error` | Lỗi cần xử lý nhưng hệ thống vẫn chạy | Order save failed, Validation error |
| `warning` | Cảnh báo, vấn đề tiềm ẩn | Low stock, Deprecated function |
| `notice` | Thông báo điều bất thường | Unusual activity |
| `info` | Thông tin hoạt động bình thường | Order created, User logged in |

## Best Practices

### ✅ DO:
- Log lỗi quan trọng ảnh hưởng đến business logic
- Log thao tác nhạy cảm (xóa, cập nhật trạng thái)
- Bao gồm context đầy đủ (IDs, user, timestamp)
- Dùng correct log level
- Log security issues

### ❌ DON'T:
- Log mọi search request (quá nhiều)
- Log password hoặc sensitive data
- Log trong vòng lặp lớn
- Dùng error level cho info logs
- Block request nếu logging fails

## Performance

- **File-based**: Không ảnh hưởng database
- **Async writing**: Không block request
- **Auto cleanup**: Xóa log > 90 ngày
- **Module separation**: Mỗi module riêng folder
- **JSONL format**: Đọc từng dòng, không load toàn file

## Troubleshooting

### Không thấy logs?
1. Check plugin `tgs-error-logger` đã activate chưa
2. Check thư mục `/uploads/sites/{blog_id}/tgs_logs/` có quyền write
3. Check integration file đã load (xem `tgs-error-logger.php`)

### Logs quá nhiều?
1. Giảm log level (chỉ log error trở lên)
2. Tắt log cho search/datatable requests
3. Chạy cleanup: `TGS_Error_Logger::instance()->cleanup_old_logs(30);`

### Muốn thêm context vào log?
```php
do_action('tgs_pos_error', 'Order failed', [
    'order_id' => $order_id,
    'customer_id' => $customer_id,
    'total' => $total,
    'payment_method' => $payment_method,
    'items' => $items, // Có thể log array
    'reason' => $error_message
]);
```

## Future Extensions

Để thêm module mới, edit `class-tgs-error-logger.php`:
```php
private static $modules = [
    // ... existing
    'your_module' => 'Module Description',
];
```

Và tạo helper function trong `helpers.php`:
```php
function tgs_log_your_module_error($message, $context = []) {
    return TGS_Error_Logger_Handler::log_error('your_module', $message, $context, 'error');
}
```
