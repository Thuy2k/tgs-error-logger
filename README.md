# TGS Error Logger

**Version**: 1.0.0  
**Status**: ✅ Production Ready

WordPress Multisite logging system theo dõi operations từ 4 plugins: tgs_pos, tgs_shop_management, tgs_purchase, tgs_selling_policy.

---

## 📦 Quick Info

- **198 operations** được log (CUD operations only, không log Read/List)
- **Module-based**: Mỗi plugin có log riêng (pos/, shop/, purchase/, selling_policy/)
- **JSONL format**: 1 dòng = 1 JSON object
- **Non-invasive**: Hook vào AJAX actions, không modify plugin code gốc
- **Silent failure**: Try-catch wrap all, không block business logic

---

## 🚀 Deployment

```bash
# 1. Upload plugin
# 2. Activate: Plugins → TGS Error Logger
# 3. View logs: Tools → Error Logger
```

**Test**: Tạo 1 POS order → Check logs có entry mới trong module "pos"

---

## 📊 Coverage

| Plugin | Operations Logged | Notes |
|--------|-------------------|-------|
| tgs_pos | 50 | Orders, customers, returns, exchanges, transfers |
| tgs_shop_management | 66 | Products, inventory, categories, import/export |
| tgs_purchase | 60 | PO, suppliers, receiving |
| tgs_selling_policy | 22 | Policies, groups, AI operations |

**Why not 100%?** Chỉ log CUD operations (Create/Update/Delete). Read operations (list/search/get) không log vì quá nhiều requests.

---

## 📁 Log Structure

```
wp-content/uploads/tgs_logs/
├── pos/2026-07-25.log
├── shop/2026-07-25.log
├── purchase/2026-07-25.log
└── selling_policy/2026-07-25.log
```

**Log entry format (JSONL)**:
```json
{"timestamp":"2026-07-25 13:15:59","level":"info","module":"pos","action":"Order save started","data":{"items_count":3,"total":150000},"user_id":1,"ip":"42.116.164.152"}
```

---

## 🔧 Files Structure

```
tgs-error-logger/
├── tgs-error-logger.php          # Main plugin file
├── includes/
│   ├── class-tgs-error-logger.php         # Core logger class
│   ├── class-tgs-error-logger-handler.php # Error handler
│   ├── class-tgs-error-logger-admin.php   # Admin interface
│   └── helpers.php                        # Helper functions
├── integrations/
│   ├── integration-pos.php                # 50 hooks
│   ├── integration-shop.php               # 66 hooks
│   ├── integration-purchase.php           # 60 hooks
│   └── integration-selling-policy.php     # 22 hooks
└── templates/
    └── admin-page.php                     # Log viewer UI
```

---

## ✅ Verification Status

```
✅ Syntax: All files pass php -l
✅ Hooks-Functions: 100% match (no missing methods)
✅ Integration files: 198 hooks ↔ 198 functions
✅ Error handling: Try-catch + null coalescing operators
```

---

## 🐛 Common Issues

**"Undefined array key 'module_name'"**  
→ Fixed với `?? 'unknown'` operator

**"call_user_func_array(): method does not exist"**  
→ Fixed: All hooks có functions tương ứng

**Logs không xuất hiện**  
→ Check permissions: `chmod -R 755 wp-content/uploads/tgs_logs/`

---

## 📝 What Gets Logged

✅ **YES** (CUD operations):
- Order create/update, customer save
- Product create/update/delete
- Inventory updates, transfers
- PO create/commit/approve
- Policy save/delete
- Destructive operations (delete, commit) → level "warning"

❌ **NO** (Read operations):
- List orders, search products
- Get customer info, get stats
- Dashboard queries
- *Too many requests, không cần cho audit trail*

---

## 💾 Storage

- **~1 MB/store/day** × 650 stores = ~650 MB/day
- **30-day retention** = ~20 GB/month
- **Manual cleanup**: `find wp-content/uploads/tgs_logs/ -name "*.log" -mtime +30 -delete`

---

## 🎯 Use Cases

1. **Debug production**: Trace operations khi user báo lỗi
2. **Audit trail**: Ai đã delete order? Ai approve payment?
3. **Performance**: Operation nào chậm? Peak hours nào?
4. **Business insights**: Feature nào được dùng nhiều?

---

## 📞 Support

- Check `wp-content/debug.log` cho PHP errors
- Admin interface: **Tools → Error Logger**
- Deactivate plugin nếu gặp critical issues (business logic vẫn chạy)

---

**Ready to deploy!** 🚀
