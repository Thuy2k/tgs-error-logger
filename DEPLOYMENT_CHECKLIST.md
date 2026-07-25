# TGS Error Logger - Deployment Checklist

## ✅ Pre-Deployment Verification (COMPLETED)

### 1. Syntax Check
- ✅ **tgs-error-logger.php** - No syntax errors
- ✅ **class-tgs-error-logger.php** - No syntax errors
- ✅ **class-tgs-error-logger-handler.php** - No syntax errors
- ✅ **class-tgs-error-logger-reader.php** - No syntax errors
- ✅ **class-tgs-error-logger-admin.php** - No syntax errors
- ✅ **helpers.php** - No syntax errors

### 2. Integration Files Check
- ✅ **integration-pos.php** - 50 hooks ↔ 50 functions ✓
- ✅ **integration-shop.php** - 66 hooks ↔ 66 functions ✓
- ✅ **integration-purchase.php** - 60 hooks ↔ 60 functions ✓
- ✅ **integration-selling-policy.php** - 22 hooks ↔ 22 functions ✓

### 3. Total Coverage
- **198 AJAX operations** được log across 4 plugins:
  - tgs_pos: 50 operations
  - tgs_shop_management: 66 operations (out of 251 total)
  - tgs_purchase: 60 operations
  - tgs_selling_policy: 22 operations

---

## 📋 Deployment Steps

### Step 1: Backup
```bash
# Backup plugin hiện tại (nếu có)
cp -r wp-content/plugins/tgs-error-logger wp-content/plugins/tgs-error-logger.backup.$(date +%Y%m%d)

# Hoặc trên Windows
xcopy wp-content\plugins\tgs-error-logger wp-content\plugins\tgs-error-logger.backup.20260725 /E /I
```

### Step 2: Upload Plugin
```bash
# Upload toàn bộ folder tgs-error-logger lên server
# Đường dẫn: wp-content/plugins/tgs-error-logger/
```

### Step 3: Set Permissions (Linux/Unix only)
```bash
chmod -R 755 wp-content/plugins/tgs-error-logger/
chmod 644 wp-content/plugins/tgs-error-logger/*.php
```

### Step 4: Activate Plugin
1. Đăng nhập WordPress Admin
2. Vào **Plugins → Installed Plugins**
3. Tìm **TGS Error Logger**
4. Click **Activate**

### Step 5: Verify Logs Directory
```bash
# Kiểm tra thư mục logs đã tự động tạo chưa
ls -la wp-content/uploads/tgs_logs/

# Nếu chưa có, tạo thủ công:
mkdir -p wp-content/uploads/tgs_logs
chmod 755 wp-content/uploads/tgs_logs
```

---

## 🧪 Post-Deployment Testing

### Test 1: Error Logging (Core)
1. Vào **Tools → Error Logger**
2. Chọn module **system**
3. Xem có log entries không
4. Trigger 1 lỗi test (ví dụ: access không tồn tại URL)
5. Verify log mới xuất hiện

### Test 2: POS Logging
1. Mở POS interface
2. Thực hiện 1 operation: **Save Order**
3. Vào **Tools → Error Logger → Module: pos**
4. Verify có log entry với:
   - ✅ Action: "Order save started"
   - ✅ Level: info
   - ✅ Data: items_count, total, payment_method

### Test 3: Shop Logging
1. Vào Shop Management
2. Thực hiện: **Create new product**
3. Vào **Tools → Error Logger → Module: shop**
4. Verify có log entry với:
   - ✅ Action: "Product quick add started"
   - ✅ Level: info

### Test 4: Purchase Logging
1. Vào Purchase module
2. Thực hiện: **Create PO**
3. Vào **Tools → Error Logger → Module: purchase**
4. Verify có log entry

### Test 5: Selling Policy Logging
1. Vào Selling Policy
2. Thực hiện: **Save policy**
3. Vào **Tools → Error Logger → Module: selling_policy**
4. Verify có log entry

---

## 🔍 Troubleshooting

### Issue 1: "Plugin activation failed"
**Cause**: Syntax error trong PHP files
**Solution**: 
```bash
# Check syntax từng file
php -l wp-content/plugins/tgs-error-logger/tgs-error-logger.php
```

### Issue 2: "Logs không xuất hiện"
**Cause**: Permission issues hoặc directory không tồn tại
**Solution**:
```bash
# Check permissions
ls -la wp-content/uploads/tgs_logs/

# Fix permissions
chmod -R 755 wp-content/uploads/tgs_logs/
```

### Issue 3: "Fatal error: call_user_func_array"
**Cause**: Hook name không match với function name
**Solution**: 
- Đã fix! Tất cả 198 hooks đã match với functions
- Nếu vẫn gặp, check file debug.log để xem action name

### Issue 4: "Undefined array key 'module_name'"
**Cause**: Log entry thiếu module_name
**Solution**: 
- Đã fix! Đã thêm `?? 'unknown'` ở admin-page.php:382

---

## 📊 Monitoring After Deployment

### Day 1: Check Logs
```bash
# Check log files được tạo
ls -la wp-content/uploads/tgs_logs/*/$(date +%Y-%m-%d).log

# Check file sizes
du -h wp-content/uploads/tgs_logs/
```

### Day 7: Review Coverage
1. Xem module nào có nhiều logs nhất
2. Xem có lỗi nào lặp lại không
3. Review disk usage:
   ```bash
   du -sh wp-content/uploads/tgs_logs/
   ```

### Day 30: Cleanup Check
1. Verify old logs đã auto-cleanup (nếu có cron job)
2. Nếu chưa có auto-cleanup, xóa manual:
   ```bash
   # Xóa logs > 30 days
   find wp-content/uploads/tgs_logs/ -name "*.log" -mtime +30 -delete
   ```

---

## 🎯 Success Criteria

Plugin deployment thành công khi:

- ✅ Plugin activate không lỗi
- ✅ Admin page "Tools → Error Logger" hiển thị
- ✅ Có thể xem logs của từng module
- ✅ POS operations được log vào pos/YYYY-MM-DD.log
- ✅ Shop operations được log vào shop/YYYY-MM-DD.log
- ✅ Purchase operations được log vào purchase/YYYY-MM-DD.log
- ✅ Selling policy operations được log vào selling_policy/YYYY-MM-DD.log
- ✅ Không có fatal errors trong debug.log
- ✅ Business operations vẫn chạy bình thường (logging không block)

---

## 📝 Notes

### What's Logged
- ✅ **CUD Operations** (Create, Update, Delete)
- ✅ **Financial Operations** (Payment, Order commit)
- ✅ **Destructive Operations** (Delete, Approve, Reject)
- ❌ **NOT logged**: Read/List/Search operations (too many requests)

### Log Levels
- **info**: Normal operations (save, create, update)
- **warning**: Destructive operations (delete, commit, approve)
- **error**: PHP errors, exceptions, fatal errors

### File Format
**JSONL** (JSON Lines) - mỗi dòng là 1 JSON object:
```json
{"timestamp":"2026-07-25 13:15:59","level":"info","module":"pos","action":"Order save started","data":{"items_count":3,"total":150000},"user_id":1,"ip":"42.116.164.152"}
```

### Known Limitations
1. **Shop plugin**: Chỉ cover 66/251 operations (26%)
   - Đã cover các operations quan trọng nhất (CUD)
   - Các operations read-only không được log
   - Có thể expand thêm sau

2. **Log retention**: Manual cleanup (chưa có auto-cleanup)
   - Khuyến nghị: Setup cron job để xóa logs > 30 days

3. **Performance impact**: Minimal
   - Silent failure (không block business logic)
   - Priority = 1 (log trước business logic)
   - Try-catch wrap tất cả

---

## 🚀 Ready to Deploy!

Tất cả files đã verified và sẵn sàng deploy lên production!
