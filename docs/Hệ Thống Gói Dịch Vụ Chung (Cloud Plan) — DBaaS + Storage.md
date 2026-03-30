# Hệ Thống Gói Dịch Vụ Chung (Cloud Plan) — DBaaS + Storage

## Mô tả

Gộp pricing của **Database** và **Storage** thành 1 **Cloud Plan** duy nhất, lưu trạng thái trên bảng `users`. User nâng cấp → thanh toán qua số dư tài khoản → mở khóa quota cho cả 2 dịch vụ.

---

## User Review Required

> [!NOTE]
> **Gói chung + Admin Override**: Mặc định user mua gói chung cho cả DB + Storage. Nếu chỉ muốn dùng 1 dịch vụ → liên hệ Admin để được nâng cấp riêng.

> [!NOTE]
> **Chu kỳ thanh toán**: Theo **tháng**, trừ số dư tài khoản. Nếu không đủ số dư → chuyển về gói Free. Dữ liệu của gói nâng cấp sẽ bị **tạm dừng** và có **7 ngày** để gia hạn, sau đó bị xóa vĩnh viễn.

---

## Đề Xuất Bổ Sung

### 1. Bảng `cloud_plan_orders` — Lịch sử nâng cấp/gia hạn
Thay vì chỉ lưu `cloud_plan` trên bảng `users`, nên có bảng đơn hàng riêng để:
- Lưu lịch sử thanh toán (audit trail)
- Biết user nâng cấp lúc nào, hết hạn lúc nào
- Hỗ trợ hoàn tiền nếu cần

### 2. Cột `cloud_plan_expires_at` trên bảng `users`
- Khi hết hạn → **tạm dừng (suspend)** tất cả DB/bucket vượt quota Free
- User có **7 ngày grace period** để gia hạn. Sau 7 ngày không gia hạn → **xóa vĩnh viễn** các resource bị tạm dừng

### 3. Downgrade + Grace Period khi hết hạn
- Cron job chạy hàng ngày kiểm tra `cloud_plan_expires_at`
- Hết hạn → set `cloud_plan = 'free'`, tạm dừng resource vượt quota
- `cloud_plan_grace_ends_at` = hết hạn + 7 ngày
- Sau grace period → xóa vĩnh viễn resource bị tạm dừng
- Gửi notification nhắc user gia hạn **3 ngày trước** khi hết hạn

### 4. Backup tự động
- Gói **Pro**: Backup **hàng tuần** (weekly)
- Gói **Max**: Backup **hàng ngày** (daily)
- Gói **Free**: Không có backup

### 5. Admin Override — Nâng cấp riêng từng dịch vụ
- Thêm 2 cột nullable trên bảng `users`: `cloud_db_override`, `cloud_storage_override`
- Admin có thể set override riêng cho 1 user (VD: chỉ DB lên Pro, Storage giữ Free)
- **Logic kiểm tra quota**: `cloud_db_override ?? cloud_plan` (override ưu tiên hơn)
- User muốn nâng cấp riêng → liên hệ Admin → Admin set override qua admin panel
- Override không có hết hạn (admin quản lý thủ công)

### 6. Cleanup resource không hoạt động (gói Free)
- Thêm cột `last_activity_at` trên bảng `cloud_databases` và `buckets`
- **Database**: Cron job query `pg_stat_activity` / `SHOW PROCESSLIST` để cập nhật `last_activity_at`
- **Storage**: Cập nhật `last_activity_at` khi user upload/download/truy cập file
- **Ngưỡng**: 30 ngày không hoạt động (chỉ áp dụng gói Free, Pro/Max được bảo vệ)
- **Flow**:
  1. Sau 30 ngày không hoạt động → gửi email/notification cảnh báo, set `status = 'inactive_warning'`
  2. Sau thêm 7 ngày vẫn không hoạt động (tổng 37 ngày) → xóa vĩnh viễn
  3. Nếu user quay lại truy cập trong thời gian cảnh báo → reset `last_activity_at`, xóa warning


---

## Proposed Changes

### Bảng giá 3 gói

| Tính năng | Free | Pro (99.000đ/tháng) | Max (399.000đ/tháng) |
|-----------|------|---------------------|----------------------|
| **DATABASE** | | | |
| Số DB tối đa | 1 | 5 | 20 |
| Dung lượng/DB | 50 MB | 500 MB | 5 GB |
| Connection limit | 3 | 20 | 50 |
| Engine | MySQL only | MySQL + PostgreSQL | MySQL + PostgreSQL |
| **STORAGE** | | | |
| Số buckets | 2 | 10 | 50 |
| Tổng dung lượng | 200 MB | 5 GB | 50 GB |
| File tối đa | 5 MB/file | 50 MB/file | 500 MB/file |
| CDN | ❌ | ✅ | ✅ Custom domain |
| **BACKUP** | | | |
| Database backup | ❌ | Hàng tuần | Hàng ngày |
| Storage backup | ❌ | Hàng tuần | Hàng ngày |
| **CHUNG** | | | |
| API Keys | 1 | 5 | 20 |
| Hỗ trợ | Cộng đồng | Email | Ưu tiên |
| Grace period | — | 7 ngày | 7 ngày |

---

### Database Schema

#### [MODIFY] Bảng `users` — Thêm cột
```
cloud_plan                VARCHAR(10)  DEFAULT 'free'  -- Gói chung: 'free' | 'pro' | 'max'
cloud_plan_expires_at     TIMESTAMP    NULLABLE        -- NULL = không hết hạn (free)
cloud_plan_grace_ends_at  TIMESTAMP    NULLABLE        -- Hết grace period → xóa resource
cloud_db_override         VARCHAR(10)  NULLABLE        -- Admin override riêng cho DB
cloud_storage_override    VARCHAR(10)  NULLABLE        -- Admin override riêng cho Storage
```

**Logic kiểm tra quota:**
```php
// Quota Database = override ưu tiên hơn gói chung
$dbPlan = $user->cloud_db_override ?? $user->cloud_plan;
$dbLimits = config("cloud_plan.plans.{$dbPlan}");

// Quota Storage = tương tự
$storagePlan = $user->cloud_storage_override ?? $user->cloud_plan;
$storageLimits = config("cloud_plan.plans.{$storagePlan}");
```

#### [NEW] Bảng `cloud_plan_orders`
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | BIGINT PK | |
| `user_id` | FK → users | Người mua |
| `order_code` | VARCHAR UNIQUE | Mã đơn (ULID) |
| `plan` | VARCHAR | 'pro' / 'max' |
| `action` | VARCHAR | 'upgrade' / 'renew' / 'downgrade' |
| `amount` | INT | Số tiền đã trừ |
| `balance_before` | DECIMAL | Số dư trước giao dịch |
| `balance_after` | DECIMAL | Số dư sau giao dịch |
| `starts_at` | TIMESTAMP | Bắt đầu hiệu lực |
| `expires_at` | TIMESTAMP | Hết hạn |
| `created_at` | TIMESTAMP | |

---

### Config — `config/cloud_plan.php`

```php
return [
    'plans' => [
        'free' => [
            'price'             => 0,
            'label'             => 'Free',
            // Database
            'max_databases'     => 1,
            'max_db_storage_mb' => 50,
            'max_connections'   => 3,
            'engines'           => ['mysql'],
            // Storage
            'max_buckets'       => 2,
            'max_storage_mb'    => 200,
            'max_file_size_mb'  => 5,
            'cdn_enabled'       => false,
            // Chung
            'max_api_keys'      => 1,
        ],
        'pro' => [
            'price'             => 99000,
            'label'             => 'Pro',
            'max_databases'     => 5,
            'max_db_storage_mb' => 500,
            'max_connections'   => 20,
            'backup'            => 'weekly',
            'engines'           => ['mysql', 'postgresql'],
            'max_buckets'       => 10,
            'max_storage_mb'    => 5120,
            'max_file_size_mb'  => 50,
            'cdn_enabled'       => true,
            'max_api_keys'      => 5,
        ],
        'max' => [
            'price'             => 399000,
            'label'             => 'Max',
            'max_databases'     => 20,
            'max_db_storage_mb' => 5120,
            'max_connections'   => 50,
            'backup'            => 'daily',
            'engines'           => ['mysql', 'postgresql'],
            'max_buckets'       => 50,
            'max_storage_mb'    => 51200,
            'max_file_size_mb'  => 500,
            'cdn_enabled'       => true,
            'max_api_keys'      => 20,
        ],
    ],
];
```

---

### Flow Nâng Cấp / Gia Hạn

```
User nhấn "Nâng cấp Pro"
  → Kiểm tra số dư >= 99.000đ
  → DB::transaction:
      1. Trừ số dư user
      2. Tạo record cloud_plan_orders (action = 'upgrade')
      3. Cập nhật users.cloud_plan = 'pro'
      4. Cập nhật users.cloud_plan_expires_at = now() + 30 ngày
      5. Xóa cloud_plan_grace_ends_at (nếu có)
      6. Kích hoạt lại resource bị tạm dừng (nếu có)
      7. Tạo AuditLog
      8. Gửi notification thành công
  → Redirect + toast "Nâng cấp thành công!"
```

### Flow Hết Hạn (Cron Job hàng ngày)

```
Cron: php artisan cloud-plan:check-expiry (chạy hàng ngày)

1. Nhắc gia hạn (3 ngày trước):
   Với user có cloud_plan_expires_at trong 3 ngày tới:
     → Gửi notification nhắc gia hạn

2. Hết hạn → Tạm dừng:
   Với user có cloud_plan_expires_at <= now() && chưa downgrade:
     → Set cloud_plan = 'free'
     → Set cloud_plan_grace_ends_at = now() + 7 ngày
     → Tạm dừng (suspend) DB/bucket vượt quota Free
     → Gửi notification: "Gói đã hết hạn, bạn có 7 ngày để gia hạn"

3. Hết grace period → Xóa:
   Với user có cloud_plan_grace_ends_at <= now():
     → Xóa vĩnh viễn resource đang bị tạm dừng
     → Set cloud_plan_grace_ends_at = NULL
     → Gửi notification: "Dữ liệu đã bị xóa do không gia hạn"
```

---

### Giao diện

#### Trang Pricing chung (có thể đặt ở `/apps/cloud-plan`)
- Hiển thị so sánh 3 gói
- Highlight gói đang dùng
- Nút nâng cấp → modal xác nhận thanh toán

#### Sidebar trang Database + Storage
- Hiện badge gói hiện tại (ưu tiên override nếu có)
- Quota hiển thị theo gói thực tế: `override ?? cloud_plan`
- Link "Nâng cấp" dẫn tới tab pricing

#### Admin Panel
- Trong trang quản lý user → thêm dropdown "Cloud DB Override" và "Cloud Storage Override"
- Admin chọn gói override (hoặc để trống = dùng gói chung)

#### Pricing component
- Thêm ghi chú: "Chỉ cần 1 dịch vụ? Liên hệ Admin"

---

## Quyết Định Đã Xác Nhận

- ✅ Giá: **Pro = 99.000đ/tháng**, **Max = 399.000đ/tháng**
- ✅ Chu kỳ: **Thanh toán theo tháng**, trừ số dư
- ✅ Hết hạn: **Tạm dừng resource** + **7 ngày grace period** → xóa vĩnh viễn
- ✅ Pricing hiển thị **chung trên cả 2 trang** Database + Storage
- ✅ Backup: Pro **hàng tuần**, Max **hàng ngày**
- ✅ Auto-renew: **Chưa cần**, giai đoạn đầu chỉ làm giao diện cứng
- ✅ Admin Override: 2 cột `cloud_db_override` + `cloud_storage_override` để nâng cấp riêng
- ✅ User muốn dùng 1 dịch vụ → liên hệ Admin

## Verification Plan

### Manual Verification
1. Tạo user mới → mặc định Free → kiểm tra quota đúng
2. Nâng cấp Pro → trừ tiền → quota mở rộng
3. Để hết hạn → downgrade tự động → quota thu hẹp
4. Kiểm tra giao diện hiển thị đúng gói trên cả 2 trang Database + Storage
