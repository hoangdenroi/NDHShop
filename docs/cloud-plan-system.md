# Hệ Thống Cloud Plan — DBaaS + Storage (Tài Liệu Thống Nhất)

> Tài liệu gốc duy nhất cho toàn bộ module Cloud Plan. Mọi thiết kế, schema, flow, config đều tham chiếu từ đây.

---

## 1. Tổng Quan

### Mục tiêu
Xây dựng nền tảng **SaaS** tích hợp vào NDHShop, cho phép user:
- **DBaaS**: Khởi tạo nhanh database PostgreSQL / MySQL qua Web Dashboard + REST API
- **Storage**: Lưu trữ file tương tự S3 (upload, quản lý, serve qua CDN mini)
- Thanh toán qua **số dư tài khoản** (đã có sẵn trong NDHShop)

### Đối tượng sử dụng
- Developer cá nhân / freelancer cần DB nhanh cho side project
- Startup nhỏ cần hạ tầng DB không muốn tự quản lý
- Team dev nội bộ, Sinh viên cần môi trường dev/staging nhanh

### Phân pha triển khai
- **Phase 1**: DBaaS (Database as a Service) — hiện tại
- **Phase 2**: S3-like Storage — sau khi DBaaS ổn định

### Gói dịch vụ thống nhất
Gộp pricing của **Database** và **Storage** thành 1 **Cloud Plan** duy nhất, lưu trạng thái trên bảng `users`. User nâng cấp → thanh toán qua số dư → mở khóa quota cho cả 2 dịch vụ.

---

## 2. Kiến Trúc Hệ Thống

### A. Mini DBaaS

```
┌──────────────────────────────────────────────────┐
│                    USERS                          │
│       (Web Dashboard / API Client / SDK)          │
└────────────────┬─────────────────────────────────┘
                 │
┌────────────────▼─────────────────────────────────┐
│          WEB PORTAL (Blade + Alpine.js)            │
└────────────────┬─────────────────────────────────┘
                 │
┌────────────────▼─────────────────────────────────┐
│        LARAVEL BACKEND (Control Plane)             │
│  ┌──────────┐ ┌──────────┐ ┌──────────────────┐  │
│  │Auth      │ │Billing   │ │DB Management     │  │
│  │(Session) │ │(Balance) │ │(Provisioning)    │  │
│  └──────────┘ └──────────┘ └──────────────────┘  │
└────────────────┬─────────────────────────────────┘
                 │
┌────────────────▼─────────────────────────────────┐
│       PROVISIONING SERVICE (Queue-based)           │
│  ┌─────────────────┐  ┌────────────────────────┐  │
│  │PostgresProvisioner│ │MysqlProvisioner       │  │
│  │(pgsql_master)    │ │(mysql_master)          │  │
│  └─────────────────┘  └────────────────────────┘  │
└────────────────┬─────────────────────────────────┘
                 │
┌────────────────▼─────────────────────────────────┐
│     DATABASE ENGINES (Shared Instance)             │
│  ├── PostgreSQL (port 5432)                        │
│  └── MySQL      (port 3306)                        │
└──────────────────────────────────────────────────┘
```

> **Giai đoạn đầu**: Dùng **Shared Instance** (1 cluster, phân quyền bằng User DB) để tiết kiệm RAM. Sau này nâng cấp: `Shared Instance → Multi Node → Per Customer Instance`.

### B. S3-like Storage (Phase 2)

```
Browser ──▶ Laravel Backend ──▶ Local File System (Storage)
                                      │
                               Nginx (CDN mini - serve static file)
```

Quản lý theo `Buckets` (thùng chứa) và `Objects` (file).

---

## 3. Bảng Giá — 3 Gói Dịch Vụ

| Tính năng | Free | Pro (99.000đ/tháng) | Max (399.000đ/tháng) |
|-----------|------|---------------------|----------------------|
| **DATABASE** | | | |
| Số DB tối đa | 1 | 5 | 15 |
| Dung lượng/DB | 50 MB | 500 MB | 3 GB |
| Connection limit | 3 | 20 | 50 |
| Engine | MySQL only | MySQL + PostgreSQL | MySQL + PostgreSQL |
| **STORAGE** | | | |
| Số buckets | 2 | 10 | 30 |
| Tổng dung lượng | 200 MB | 5 GB | 30 GB |
| File tối đa | 5 MB/file | 100 MB/file | 500 MB/file |
| CDN | ❌ | ✅ | ✅ Custom domain |
| **BACKUP** | | | |
| Database backup | ❌ | Hàng tuần | Hàng ngày |
| Storage backup | ❌ | Hàng tuần | Hàng ngày |
| **CHUNG** | | | |
| API Keys | 1 | 3 | 10 |
| Hỗ trợ | Cộng đồng | Email | Ưu tiên |
| Grace period | — | 7 ngày | 7 ngày |

---

## 4. Chu Kỳ Thanh Toán & Chiết Khấu

Thanh toán qua **số dư tài khoản**. Hỗ trợ 4 chu kỳ:

| Chu kỳ | Số tháng | Chiết khấu | Giá Pro | Giá Max |
|--------|----------|-----------|---------|---------|
| 1 tháng | 1 | 0% | 99.000đ | 399.000đ |
| 3 tháng | 3 | 5% | 282.150đ | 1.137.150đ |
| 6 tháng | 6 | 10% | 534.600đ | 2.154.600đ |
| 1 năm | 12 | 20% | 950.400đ | 3.832.800đ |

> **Công thức**: `giá_gốc × số_tháng × (1 - chiết_khấu)`

---

## 5. Quyết Định Đã Xác Nhận

- ✅ **Tên gói**: Free / Pro / Max
- ✅ **Giá gốc/tháng**: Pro = 99.000đ, Max = 399.000đ
- ✅ **Chu kỳ**: 1 tháng / 3 tháng / 6 tháng / 1 năm
- ✅ **Chiết khấu**: 3th = -5%, 6th = -10%, 1 năm = -20%
- ✅ **Gia hạn sớm**: **Cộng dồn** thời gian (`expires_at` cũ + số tháng mới)
- ✅ **Downgrade giữa chừng**: **Hoàn 70%** giá trị còn lại, tạm dừng gói, user tự chọn gói mới
- ✅ **Upgrade giữa chừng**: Cho phép (tính credit từ gói cũ, bù trừ vào gói mới)
- ✅ **Hết hạn**: Tạm dừng resource + 7 ngày grace period → xóa vĩnh viễn
- ✅ **Pricing**: Hiển thị chung trên cả 2 trang Database + Storage
- ✅ **Backup**: Pro = hàng tuần, Max = hàng ngày
- ✅ **Admin Override**: 2 cột `cloud_db_override` + `cloud_storage_override`
- ✅ **User chỉ muốn 1 dịch vụ**: Liên hệ Admin để override riêng
- ✅ **Auto-renew**: Chưa cần giai đoạn đầu, chỉ làm giao diện cứng

---

## 6. Database Schema

### 6.1. Bảng `users` — Thêm cột Cloud Plan

```
cloud_plan                  VARCHAR(10)  DEFAULT 'free'     -- 'free' | 'pro' | 'max'
cloud_plan_billing_cycle    VARCHAR(10)  DEFAULT 'monthly'  -- 'monthly' | 'quarterly' | 'semiannual' | 'annual'
cloud_plan_expires_at       TIMESTAMP    NULLABLE           -- NULL = free (không hết hạn)
cloud_plan_grace_ends_at    TIMESTAMP    NULLABLE           -- Hết grace → xóa resource
cloud_db_override           VARCHAR(10)  NULLABLE           -- Admin override riêng cho DB
cloud_storage_override      VARCHAR(10)  NULLABLE           -- Admin override riêng cho Storage
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

### 6.2. Bảng `cloud_plan_orders` — Lịch sử thanh toán

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | BIGINT PK | |
| `user_id` | FK → users | Người mua |
| `order_code` | VARCHAR UNIQUE | Mã đơn (ULID) |
| `plan` | VARCHAR(10) | 'pro' / 'max' |
| `action` | VARCHAR(20) | 'upgrade' / 'renew' / 'downgrade' / 'refund' |
| `billing_cycle` | VARCHAR(10) | 'monthly' / 'quarterly' / 'semiannual' / 'annual' |
| `months` | INT | Số tháng hiệu lực: 1 / 3 / 6 / 12 |
| `original_amount` | INT | Giá gốc (chưa giảm) |
| `discount_percent` | INT DEFAULT 0 | % giảm giá đã áp dụng |
| `amount` | INT | Số tiền thực trả (sau giảm), hoàn (-) |
| `balance_before` | DECIMAL | Số dư trước giao dịch |
| `balance_after` | DECIMAL | Số dư sau giao dịch |
| `starts_at` | TIMESTAMP | Bắt đầu hiệu lực |
| `expires_at` | TIMESTAMP | Hết hạn |
| `note` | TEXT NULLABLE | Ghi chú (VD: lý do hoàn tiền) |
| `created_at` | TIMESTAMP | |

### 6.3. Bảng `cloud_databases` — Metadata DB instances

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | BIGINT PK | |
| `user_id` | FK → users | Chủ sở hữu |
| `unitcode` | VARCHAR UNIQUE | ULID |
| `engine` | VARCHAR(15) | 'postgresql' / 'mysql' |
| `db_name` | VARCHAR(100) | Tên DB thực tế (prefix `ndh_`) |
| `db_user` | VARCHAR(100) | Username DB |
| `db_password_encrypted` | TEXT | Mật khẩu mã hóa AES |
| `host` | VARCHAR(255) | Host kết nối |
| `port` | INT | 5432 (PG) / 3306 (MySQL) |
| `status` | VARCHAR(20) | 'provisioning' / 'active' / 'suspended' / 'deleting' / 'deleted' |
| `max_connections` | INT | Giới hạn connection |
| `max_storage_mb` | INT | Giới hạn dung lượng |
| `storage_used_mb` | DECIMAL | Dung lượng đã dùng |
| `last_activity_at` | TIMESTAMP NULLABLE | Lần hoạt động cuối (cho cleanup Free) |
| `expires_at` | TIMESTAMP NULLABLE | — |
| `is_deleted` | BOOLEAN | Soft delete |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

### 6.4. Bảng `api_keys`

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | BIGINT PK | |
| `user_id` | FK → users | Chủ sở hữu |
| `name` | VARCHAR | Label (VD: "Production Key") |
| `key` | VARCHAR(64) UNIQUE | Hashed |
| `last_used_at` | TIMESTAMP NULLABLE | |
| `is_active` | BOOLEAN DEFAULT true | |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

### 6.5. Bảng `buckets` (Phase 2 — Storage)

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | BIGINT PK | |
| `user_id` | FK → users | Chủ sở hữu |
| `name` | VARCHAR(100) | Tên bucket |
| `is_public` | BOOLEAN DEFAULT false | Cho phép truy cập công khai |
| `storage_used_mb` | DECIMAL | Dung lượng đã dùng |
| `last_activity_at` | TIMESTAMP NULLABLE | Cho cleanup Free |
| `created_at` | TIMESTAMP | |

### 6.6. Bảng `objects` (Phase 2 — File trong bucket)

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | BIGINT PK | |
| `bucket_id` | FK → buckets | Bucket chứa |
| `key` | VARCHAR(500) | Đường dẫn file (VD: `images/logo.png`) |
| `mime_type` | VARCHAR(100) | Loại file |
| `size_bytes` | BIGINT | Kích thước |
| `checksum` | VARCHAR(64) | Hash kiểm tra toàn vẹn |
| `created_at` | TIMESTAMP | |

---

## 7. Config — `config/cloud_plan.php`

```php
return [
    // Chu kỳ thanh toán & chiết khấu
    'billing_cycles' => [
        'monthly'    => ['label' => '1 tháng',  'months' => 1,  'discount' => 0],
        'quarterly'  => ['label' => '3 tháng',  'months' => 3,  'discount' => 5],
        'semiannual' => ['label' => '6 tháng',  'months' => 6,  'discount' => 10],
        'annual'     => ['label' => '1 năm',    'months' => 12, 'discount' => 20],
    ],

    // Tỉ lệ hoàn tiền khi downgrade giữa chừng
    'refund_rate' => 70, // hoàn 70% giá trị còn lại

    // Gói dịch vụ
    'plans' => [
        'free' => [
            'price'             => 0,
            'label'             => 'Free',
            // Database
            'max_databases'     => 1,
            'max_db_storage_mb' => 50,
            'max_connections'   => 3,
            'engines'           => ['mysql'],
            'backup'            => null,
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
            'max_file_size_mb'  => 100,
            'cdn_enabled'       => true,
            'max_api_keys'      => 3,
        ],
        'max' => [
            'price'             => 399000,
            'label'             => 'Max',
            'max_databases'     => 15,
            'max_db_storage_mb' => 3072,
            'max_connections'   => 50,
            'backup'            => 'daily',
            'engines'           => ['mysql', 'postgresql'],
            'max_buckets'       => 30,
            'max_storage_mb'    => 30720,
            'max_file_size_mb'  => 500,
            'cdn_enabled'       => true,
            'max_api_keys'      => 10,
        ],
    ],
];
```

---

## 8. Config — `config/dbaas.php`

```php
return [
    // Engine mặc định
    'default_engine' => env('DBAAS_DEFAULT_ENGINE', 'mysql'),
    'engines' => ['postgresql', 'mysql'],

    // Prefix chung cho tên DB
    'db_prefix' => env('DBAAS_DB_PREFIX', 'ndh_'),

    // PostgreSQL master connection
    'pgsql' => [
        'host'     => env('DBAAS_PGSQL_HOST', '127.0.0.1'),
        'port'     => env('DBAAS_PGSQL_PORT', 5432),
        'admin'    => env('DBAAS_PGSQL_ADMIN_USER', 'postgres'),
        'password' => env('DBAAS_PGSQL_ADMIN_PASSWORD', ''),
    ],

    // MySQL master connection
    'mysql' => [
        'host'     => env('DBAAS_MYSQL_HOST', '127.0.0.1'),
        'port'     => env('DBAAS_MYSQL_PORT', 3306),
        'admin'    => env('DBAAS_MYSQL_ADMIN_USER', 'root'),
        'password' => env('DBAAS_MYSQL_ADMIN_PASSWORD', ''),
    ],
];
```

---

## 9. Business Logic — Các Flow Chính

### 9.1. Tính giá thanh toán

```php
/**
 * Tính giá theo gói + chu kỳ (đã áp dụng chiết khấu)
 */
public function calculatePrice(string $plan, string $cycle): array
{
    $planConfig  = config("cloud_plan.plans.{$plan}");
    $cycleConfig = config("cloud_plan.billing_cycles.{$cycle}");

    $pricePerMonth = $planConfig['price'];
    $months        = $cycleConfig['months'];
    $discount      = $cycleConfig['discount'];

    $originalAmount = $pricePerMonth * $months;
    $discountAmount = (int) ($originalAmount * $discount / 100);
    $finalAmount    = $originalAmount - $discountAmount;

    return [
        'original_amount'  => $originalAmount,
        'discount_percent' => $discount,
        'discount_amount'  => $discountAmount,
        'final_amount'     => $finalAmount,
        'months'           => $months,
    ];
}
```

### 9.2. Flow Nâng Cấp (Upgrade)

```
User chọn "Nâng cấp Pro" + chọn chu kỳ "6 tháng"
  → Tính giá: 99.000 × 6 × 0.9 = 534.600đ
  → Kiểm tra số dư >= 534.600đ
  → DB::transaction:
      1. Trừ số dư 534.600đ
      2. Tạo cloud_plan_orders (action='upgrade', billing_cycle='semiannual', months=6)
      3. users.cloud_plan = 'pro'
      4. users.cloud_plan_billing_cycle = 'semiannual'
      5. users.cloud_plan_expires_at = now() + 6 tháng
      6. Xóa cloud_plan_grace_ends_at (nếu có)
      7. Kích hoạt lại resource bị tạm dừng (nếu có)
      8. AuditLog + Notification
  → Redirect + toast "Nâng cấp thành công!"
```

### 9.3. Flow Gia Hạn (Renew) — Cộng dồn thời gian

```
User nhấn "Gia hạn" Pro 3 tháng (gói cũ còn 15 ngày)
  → Tính giá: 99.000 × 3 × 0.95 = 282.150đ
  → Kiểm tra số dư >= 282.150đ
  → DB::transaction:
      1. Trừ số dư
      2. Tạo cloud_plan_orders (action='renew')
      3. users.cloud_plan_expires_at = expires_at_cũ + 3 tháng (CỘNG DỒN)
         → VD: còn 15 ngày + 90 ngày = 105 ngày nữa
      4. AuditLog + Notification
```

### 9.4. Flow Downgrade Giữa Chừng — Hoàn 70%

```
User đang Pro 6 tháng, đã dùng 2 tháng, còn 4 tháng
  → Tính giá trị còn lại:
      Giá đã trả: 534.600đ
      Giá trị/tháng (theo giá đã trả): 534.600 / 6 = 89.100đ
      Giá trị còn lại: 89.100 × 4 = 356.400đ
      Hoàn 70%: 356.400 × 0.7 = 249.480đ

  → User chọn gói mới (Free / Pro khác / Max)
  → DB::transaction:
      1. Cộng 249.480đ vào số dư user
      2. Tạo cloud_plan_orders (action='downgrade', amount=-249.480)
      3. Nếu chọn Free:
         - users.cloud_plan = 'free'
         - users.cloud_plan_expires_at = NULL
         - Tạm dừng resource vượt quota Free
      4. Nếu chọn gói khác → chạy flow Upgrade mới
      5. AuditLog + Notification
```

### 9.5. Flow Upgrade Giữa Chừng (Pro → Max)

```
User đang Pro 3 tháng, đã dùng 1 tháng, còn 2 tháng
  → Tính credit từ gói cũ (hoàn 70%):
      Giá đã trả Pro 3 tháng: 282.150đ
      Giá trị/tháng: 282.150 / 3 = 94.050đ
      Giá trị còn lại: 94.050 × 2 = 188.100đ
      Credit (hoàn 70%): 188.100 × 0.7 = 131.670đ

  → Hoàn credit vào số dư
  → Chạy flow Upgrade Max (trừ tiền từ số dư đã + credit)
```

### 9.6. Flow Hết Hạn (Cron Job hàng ngày)

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

### 9.7. Cleanup Resource Không Hoạt Động (Gói Free)

```
Cron: php artisan cloud-plan:cleanup-inactive (chạy hàng ngày)

Chỉ áp dụng gói Free (Pro/Max được bảo vệ):

1. Sau 30 ngày không hoạt động:
   → Gửi email/notification cảnh báo
   → Set status = 'inactive_warning'

2. Sau thêm 7 ngày vẫn không hoạt động (tổng 37 ngày):
   → Xóa vĩnh viễn

3. Nếu user quay lại truy cập trong thời gian cảnh báo:
   → Reset last_activity_at, xóa warning
```

**Cách cập nhật `last_activity_at`:**
- **Database**: Cron query `pg_stat_activity` / `SHOW PROCESSLIST`
- **Storage**: Cập nhật khi user upload/download/truy cập file

---

## 10. Services & Architecture (Laravel)

### Strategy Pattern cho Multi-Engine

```
┌──────────────────────────────────┐
│  DatabaseProvisioningService     │ ← Factory/Router
│  getProvisioner(engine)          │
└───────────┬──────────────────────┘
            │
    ┌───────▼────────┐   ┌──────────────────┐
    │PostgresProvisioner│ │MysqlProvisioner  │
    │(pgsql_master)     │ │(mysql_master)    │
    └───────────────────┘ └──────────────────┘
```

### ProvisionerInterface

```php
interface ProvisionerInterface
{
    public function createDatabase(CloudDatabase $db): void;
    public function deleteDatabase(CloudDatabase $db): void;
    public function resetPassword(CloudDatabase $db, string $newPassword): void;
    public function getDatabaseSize(CloudDatabase $db): float; // MB
    public function sanitizeName(string $input): string;
}
```

### PostgresProvisioner
- `CREATE DATABASE` + `CREATE USER` + `GRANT ALL PRIVILEGES`
- `ALTER ROLE ... CONNECTION LIMIT` để giới hạn connection
- `pg_database_size()` để check dung lượng
- Kết nối qua `config/database.php` → `pgsql_master`

### MysqlProvisioner
- `CREATE DATABASE` + `CREATE USER` + `GRANT ALL PRIVILEGES ON db.* TO user`
- `SET GLOBAL max_user_connections` để giới hạn connection
- Query `information_schema.TABLES` để check dung lượng
- Kết nối qua `config/database.php` → `mysql_master`

### DatabaseProvisioningService
Factory chọn đúng provisioner theo engine:
- `getProvisioner(string $engine): ProvisionerInterface`
- `createDatabase(CloudDatabase $db)`: Gọi provisioner tương ứng
- `deleteDatabase(CloudDatabase $db)`: Gọi provisioner tương ứng
- `sanitizeName(string $input)`: Whitelist `[a-zA-Z0-9_]`, prefix `ndh_`

### CloudPlanPaymentService
Xử lý thanh toán — tương tự `GiftPaymentService`:
- `calculatePrice(string $plan, string $cycle)`: Tính giá theo chu kỳ + chiết khấu
- `upgrade(User $user, string $plan, string $cycle)`: Nâng cấp
- `renew(User $user, string $cycle)`: Gia hạn (cộng dồn)
- `downgrade(User $user, ?string $newPlan)`: Hoàn 70% + chuyển gói
- `validateBalance(User $user, int $amount)`: Kiểm tra số dư
- Sử dụng `DB::transaction()` cho atomic operation

---

## 11. Jobs (Queue)

| Job | Mô tả |
|-----|-------|
| `ProvisionDatabaseJob` | Async tạo DB trên engine. Retry 3 lần, backoff 10s |
| `DeleteDatabaseJob` | Soft delete → Queue job xóa thật trên engine |

---

## 12. Controllers & Routes

### Web Controllers (Authenticated User)

| Controller | Method | Route | Mô tả |
|-----------|--------|-------|-------|
| `CloudDatabaseController` | `index()` | `GET /apps/cloud-databases` | Danh sách DB |
| | `store()` | `POST /apps/cloud-databases` | Tạo DB mới |
| | `show($db)` | `GET /apps/cloud-databases/{unitcode}` | Chi tiết DB |
| | `destroy($db)` | `DELETE /apps/cloud-databases/{unitcode}` | Xóa DB |
| | `resetPassword($db)` | `POST .../reset-password` | Reset pass |
| `CloudPlanController` | `upgrade()` | `POST /apps/cloud-plan/upgrade` | Nâng cấp gói |
| | `renew()` | `POST /apps/cloud-plan/renew` | Gia hạn |
| | `downgrade()` | `POST /apps/cloud-plan/downgrade` | Hạ gói |
| `ApiKeyController` | `index()` | `GET /apps/api-keys` | Danh sách key |
| | `store()` | `POST /apps/api-keys` | Tạo key |
| | `destroy($key)` | `DELETE /apps/api-keys/{key}` | Revoke key |

### API Controllers (API Key Auth)

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| `POST` | `/api/v1/dbaas/databases` | Tạo DB |
| `GET` | `/api/v1/dbaas/databases` | Danh sách |
| `GET` | `/api/v1/dbaas/databases/{id}` | Chi tiết |
| `DELETE` | `/api/v1/dbaas/databases/{id}` | Xóa |
| `POST` | `/api/v1/dbaas/databases/{id}/reset-password` | Reset pass |

### Admin Routes

| Method | Route | Mô tả |
|--------|-------|-------|
| `GET` | `/admin/cloud-databases` | Danh sách tất cả DB |
| `PATCH` | `/admin/cloud-databases/{db}/suspend` | Tạm dừng |
| `PATCH` | `/admin/cloud-databases/{db}/activate` | Kích hoạt lại |
| — | `/admin/users/{user}` | Dropdown Cloud DB/Storage Override |

### Middleware

**AuthenticateApiKey**: Xác thực API Key cho REST API
- Check header `X-API-Key`
- Tìm ApiKey model → attach user vào request
- Return 401 nếu key không hợp lệ

---

## 13. Views (Blade)

### Trang Database — `resources/views/pages/app/databases/`

| File | Mô tả |
|------|-------|
| `database-index.blade.php` | Layout chính, @include các partial |
| `_sidebar.blade.php` | Sidebar: tổng quan, menu, quota |
| `_tab-databases.blade.php` | Tab danh sách DB cards |
| `_tab-api-keys.blade.php` | Tab quản lý API Keys |
| `_modal-create.blade.php` | Modal tạo DB mới (chọn engine + plan) |
| `_modal-connection.blade.php` | Modal chi tiết kết nối |

### Component chung
| File | Mô tả |
|------|-------|
| `cloud-plan-pricing` | Component bảng giá 3 gói (dùng chung DB + Storage) |

### Sidebar
- Hiện badge gói hiện tại (ưu tiên override nếu có)
- Quota hiển thị theo gói thực tế: `override ?? cloud_plan`
- Link "Nâng cấp" dẫn tới tab pricing

### Pricing component
- Hiển thị 3 gói + 4 chu kỳ thanh toán
- Ghi chú: "Chỉ cần 1 dịch vụ? Liên hệ Admin"

---

## 14. Bảo Mật

### Chống SQL Injection khi tạo DB
> **KHÔNG BAO GIỜ** nhận raw input từ user rồi nối chuỗi! Luôn sanitize bằng whitelist `[a-zA-Z0-9_]`.

```php
// ✅ Đúng
$dbName = $this->sanitizeName($userInput); // → "ndh_ten_database"

// ❌ Sai — SQL Injection risk
DB::statement("CREATE DATABASE " . $userInput);
```

### Giới hạn Connection
```sql
-- PostgreSQL
ALTER ROLE user_001 CONNECTION LIMIT 10;

-- MySQL  
CREATE USER 'user_001'@'%' WITH MAX_USER_CONNECTIONS 10;
```

### Bảo mật Storage (Phase 2)
- Chặn Path Traversal: Filter ký tự `../`
- Validate Mime-type: Chỉ cho loại file đã đăng ký
- Giới hạn dung lượng theo Quota
- Không expose IP DB trực tiếp: Dùng Nginx reverse proxy

---

## 15. Monitoring

### Metrics cần thu thập
- Số lượng DB đã tạo (theo engine)
- Active connections (theo user)
- Slow queries
- Disk usage per customer
- CPU/RAM usage

### Stack khuyến nghị (khi scale)
- **Prometheus** + **PostgreSQL Exporter**: Thu thập metrics
- **Grafana**: Dashboard trực quan
- Alert khi resource gần hết quota

---

## 16. Admin Override

- Thêm 2 cột nullable trên `users`: `cloud_db_override`, `cloud_storage_override`
- Admin set override riêng cho 1 user (VD: chỉ DB lên Pro, Storage giữ Free)
- Logic: `cloud_db_override ?? cloud_plan` (override ưu tiên)
- Override không có hết hạn (admin quản lý thủ công)
- Admin panel: Dropdown trong trang quản lý user

---

## 17. Verification Plan

### Automated Tests
```bash
php artisan migrate --seed
php artisan tinker
# Test sanitize
app(DatabaseProvisioningService::class)->sanitizeName("test'; DROP TABLE--")
# Kết quả mong đợi: "ndh_test_DROP_TABLE"
```

### Manual Verification
1. User mới → mặc định Free → quota đúng
2. Nâng cấp Pro (3 tháng) → trừ 282.150đ → quota mở rộng → expires = +3 tháng
3. Gia hạn khi chưa hết hạn → expires cộng dồn
4. Downgrade giữa chừng → hoàn 70% → chọn gói mới
5. Để hết hạn → auto downgrade + 7 ngày grace → xóa resource
6. Tạo DB PostgreSQL + MySQL → kết nối được
7. Thử tạo quá quota → báo lỗi
8. Reset password → password mới hoạt động
9. Admin override → quota thay đổi đúng
10. Giao diện hiển thị đúng gói trên cả Database + Storage
