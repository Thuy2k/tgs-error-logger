# TGS Error Logger

Hệ thống log lỗi thông minh cho WordPress Multisite, chia theo module và dễ quản lý.

## Tính năng

✅ **Chia log theo module**: pos, inventory, sync, api, security, database, payment, auth, system  
✅ **Log theo ngày**: Mỗi ngày một file JSONL, dễ quản lý  
✅ **Auto-detect lỗi bảo mật**: Tự động phát hiện SQL injection, XSS, path traversal...  
✅ **Multisite support**: Hỗ trợ 650+ cửa hàng  
✅ **Auto capture**: Tự động bắt mọi lỗi PHP (error, warning, exception, fatal)  
✅ **UI admin đẹp**: Xem, filter, tìm kiếm, download logs  
✅ **Performance tốt**: File-based, không làm chậm database  
✅ **Dễ mở rộng**: Thêm module mới chỉ cần thêm vào array  

## Cấu trúc lưu log

```
wp-content/uploads/sites/{blog_id}/tgs_logs/
├── pos/
│   ├── 2026-07-25.jsonl
│   ├── 2026-07-24.jsonl
│   └── ...
├── inventory/
│   ├── 2026-07-25.jsonl
│   └── ...
├── security/
│   ├── 2026-07-25.jsonl
│   └── ...
└── system/
    └── ...
```

## Cài đặt

1. Copy plugin vào `wp-content/plugins/tgs-error-logger/`
2. Activate plugin trong WordPress Admin
3. Vào menu "Nhật ký lỗi" để xem logs

## Cách sử dụng

### 1. Tự động capture (không cần code)

Plugin tự động bắt tất cả lỗi PHP:
- Errors, Warnings, Notices
- Uncaught Exceptions
- Fatal Errors
- Database Errors
- AJAX Errors

### 2. Log thủ công (trong code)

#### Cách 1: Sử dụng helper functions (dễ nhất)

```php
// Log error đơn giản
tgs_log_error('pos', 'Failed to save order', [
    'order_id' => 12345,
    'reason' => 'Database timeout'
]);

// Log warning
tgs_log_warning('inventory', 'Low stock detected', [
    'product_id' => 999,
    'quantity' => 5
]);

// Log critical
tgs_log_critical('payment', 'Payment gateway failed', [
    'gateway' => 'momo',
    'amount' => 500000
]);

// Log security issue
tgs_log_security('Possible SQL injection attempt', [
    'query' => $suspicious_query,
    'user_id' => $user_id
]);

// Log theo module cụ thể
tgs_log_pos_error('POS session expired');
tgs_log_inventory_error('Stock sync failed');
tgs_log_sync_error('HT Soft API timeout');
tgs_log_api_error('REST API rate limit exceeded');
tgs_log_database_error('Query timeout', ['query' => $query]);
```

#### Cách 2: Sử dụng logger class

```php
TGS_Error_Logger::instance()->log(
    'pos',              // module
    'error',            // level: error, warning, notice, critical, info
    'Error message',    // message
    [                   // context (optional)
        'order_id' => 123,
        'custom_data' => 'value'
    ],
    [                   // options (optional)
        'file' => __FILE__,
        'line' => __LINE__,
        'function' => __FUNCTION__
    ]
);
```

#### Cách 3: Wrap function với error handling

```php
$result = tgs_with_error_logging('pos', function() use ($order_data) {
    // Code có thể throw exception
    return save_order($order_data);
});

if ($result === false) {
    // Lỗi đã được log tự động
}
```

### 3. Đọc logs

```php
$reader = TGS_Error_Logger_Reader::instance();

// Đọc log của một module
$logs = $reader->read_logs(
    $blog_id,           // Blog ID
    'pos',              // Module
    '2026-07-25',       // Date
    [                   // Filters (optional)
        'level' => 'error',
        'search' => 'timeout',
        'security_only' => true
    ]
);

// Lấy thống kê
$stats = $reader->get_summary_stats($blog_id, 'pos', '2026-07-25');

// Lấy security issues
$security_issues = $reader->get_security_issues($blog_id, 7); // 7 days

// Lấy lỗi gần đây
$recent = $reader->get_recent_errors($blog_id, 50, 'warning');
```

## Log Levels

| Level | Mô tả | Khi nào dùng |
|-------|-------|--------------|
| `critical` | Nghiêm trọng | Hệ thống không hoạt động, fatal error |
| `error` | Lỗi | Lỗi cần xử lý nhưng hệ thống vẫn chạy |
| `warning` | Cảnh báo | Vấn đề tiềm ẩn, cần chú ý |
| `notice` | Thông báo | Điều bất thường nhưng không phải lỗi |
| `info` | Thông tin | Thông tin debug |

## Modules có sẵn

| Module | Mô tả |
|--------|-------|
| `pos` | Plugin tgs_pos |
| `inventory` | Quản lý kho |
| `sync` | Đồng bộ (HT Soft, APIs) |
| `api` | REST API / AJAX |
| `security` | Lỗi bảo mật |
| `database` | Database queries |
| `payment` | Thanh toán |
| `auth` | Xác thực |
| `system` | Hệ thống chung |

## Thêm module mới

Edit file `includes/class-tgs-error-logger.php`:

```php
private static $modules = [
    'pos' => 'POS System',
    'inventory' => 'Quản lý kho',
    // ... existing modules
    'custom_module' => 'Module mới của bạn', // <- Thêm dòng này
];
```

## Format log entry (JSONL)

Mỗi dòng trong file `.jsonl` là một JSON object:

```json
{
  "timestamp": "2026-07-25 14:30:45",
  "unix_timestamp": 1721901045,
  "level": "error",
  "level_int": 4,
  "module": "pos",
  "message": "Failed to save order",
  "blog_id": 18,
  "tgs_site_code": "CH001",
  "user_id": 5,
  "user_login": "nhanvien01",
  "ip_address": "192.168.1.100",
  "request_uri": "/wp-admin/admin-ajax.php",
  "request_method": "POST",
  "user_agent": "Mozilla/5.0...",
  "file": "/path/to/file.php",
  "line": 123,
  "function": "save_order",
  "trace": "file.php:123 -> handler.php:45",
  "context": {
    "order_id": 12345,
    "custom_data": "..."
  },
  "security_issue": false
}
```

## Security Detection

Plugin tự động phát hiện các lỗi bảo mật:

- **SQL injection**: `union select`, `drop table`, `insert into`
- **XSS**: `<script>`, `javascript:`, `onerror=`
- **Path traversal**: `../`, `..%2f`
- **Auth failures**: `authentication failed`, `login failed`
- **File upload attacks**: `.php`, `.exe`, malicious files

Khi phát hiện, log sẽ được:
1. Lưu vào module gốc (ví dụ: `pos`)
2. Đồng thời lưu vào module `security`
3. Đánh dấu `security_issue` với loại lỗi cụ thể

## UI Admin

### Filter logs

- Chọn cửa hàng (multisite)
- Chọn module
- Chọn ngày
- Chọn level
- Tìm kiếm (message, file, user)
- Chỉ xem lỗi bảo mật

### Dashboard stats

- Tổng lỗi trong ngày
- Phân bố theo level
- Số lỗi bảo mật
- File lỗi nhiều nhất

### Actions

- Download log file (.jsonl)
- Xóa log cũ (cleanup)
- View context data
- View stack trace

## Maintenance

### Auto cleanup

```php
// Xóa log cũ hơn 90 ngày
TGS_Error_Logger::instance()->cleanup_old_logs(90);
```

### Manual cleanup

Vào UI admin > Click "Xóa log cũ" > Nhập số ngày

### Check log size

```php
$reader = TGS_Error_Logger_Reader::instance();
$size = $reader->get_module_size($blog_id, 'pos');
echo size_format($size);
```

## Examples

### Ví dụ 1: Log trong plugin tgs_pos

```php
// Trong ajax handler
function tgs_pos_save_order() {
    try {
        // Validate
        if (empty($_POST['items'])) {
            tgs_log_warning('pos', 'Empty order items', [
                'user_id' => get_current_user_id(),
                'request' => $_POST
            ]);
            wp_send_json_error('Đơn hàng trống');
        }

        // Save order
        $order_id = save_order($_POST);

        if (!$order_id) {
            tgs_log_error('pos', 'Failed to save order to database', [
                'items_count' => count($_POST['items']),
                'total' => $_POST['total']
            ]);
            wp_send_json_error('Không thể lưu đơn hàng');
        }

        wp_send_json_success(['order_id' => $order_id]);

    } catch (Exception $e) {
        tgs_log_critical('pos', 'Exception in save_order', [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        wp_send_json_error('Lỗi hệ thống');
    }
}
```

### Ví dụ 2: Log sync HT Soft

```php
function sync_with_htsoft() {
    $response = wp_remote_post('https://htsoft.api/sync', [
        'body' => json_encode($data)
    ]);

    if (is_wp_error($response)) {
        tgs_log_sync_error('HT Soft API request failed', [
            'error' => $response->get_error_message(),
            'endpoint' => 'https://htsoft.api/sync'
        ]);
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code !== 200) {
        tgs_log_sync_error('HT Soft API returned error', [
            'status_code' => $code,
            'response' => wp_remote_retrieve_body($response)
        ]);
        return false;
    }

    return true;
}
```

### Ví dụ 3: Tích hợp với existing error handling

```php
// Trong wp-config.php hoặc mu-plugin
add_action('plugins_loaded', function() {
    // Chuyển hướng error_log() sang TGS logger
    if (function_exists('tgs_log_error')) {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            $module = TGS_Error_Logger::detect_module_from_path($errfile);
            tgs_log_error($module, $errstr, [
                'errno' => $errno,
                'file' => $errfile,
                'line' => $errline
            ]);
            return true;
        });
    }
}, 999);
```

## Performance

- File-based logging: không làm chậm database
- Mỗi module một thư mục riêng: đọc nhanh
- JSONL format: đọc từng dòng, không cần load toàn bộ file
- Auto cleanup: tự động xóa log cũ
- Optimized for 650+ sites multisite

## Bảo mật

- Thư mục log được bảo vệ bằng `.htaccess`
- Chỉ admin mới xem được logs
- Log không chứa password/secret (cần sanitize trước khi log)
- Security issues được highlight riêng

## Hỗ trợ

- Tác giả: TGS Team
- Version: 1.0.0

## Changelog

### 1.0.0 (2026-07-25)
- Initial release
- Multi-module logging
- Auto PHP error capture
- Security detection
- UI admin với filter/search
- Helper functions
- Multisite support
