# Kế Hoạch Triển Khai: Module DBaaS trong NDHShop

## Mô tả

Tích hợp module **Mini DBaaS (Database as a Service)** vào NDHShop, cho phép user tạo và quản lý database **PostgreSQL** và **MySQL** thông qua Web Dashboard. Thanh toán qua số dư tài khoản (đã có sẵn). Module này tận dụng pattern hiện có của NDHShop: `BaseModel` (unitcode/soft delete), `GiftPaymentService` (balance payment), `TopupController` (nạp tiền SePay).

## User Review Required

> [!IMPORTANT]
> **PostgreSQL + MySQL trên VPS**: Module này yêu cầu cài PostgreSQL và/hoặc MySQL trên VPS riêng. Laravel vẫn dùng MySQL/SQLite cho metadata, nhưng sẽ kết nối qua secondary connections (`pgsql_master`, `mysql_master`) để provisioning DB cho user. User sẽ chọn engine khi tạo DB.

> [!WARNING]
> **Giai đoạn 1 chỉ làm DBaaS**, chưa làm S3-like Storage. Storage sẽ là phase 2 riêng biệt.

---

## Proposed Changes

### Database & Models

#### [NEW] [create_cloud_databases_table.php](file:///d:/code/apps/NDHShop/database/migrations/2026_03_28_200000_create_cloud_databases_table.php)
Bảng lưu metadata các database instance đã tạo:
- `id`, `user_id` (FK), `unitcode` (ULID)
- **`engine`**: `postgresql` / `mysql` — Loại database engine
- `db_name`, `db_user`, `db_password_encrypted` (mã hóa AES)
- `host`, `port` (default 5432 cho PG, 3306 cho MySQL)
- `status`: `provisioning` / `active` / `suspended` / `deleting` / `deleted`
- `plan`: `free` / `pro` / `team`
- `max_connections` (INT), `max_storage_mb` (INT)
- `storage_used_mb` (DECIMAL), `expires_at` (nullable)
- `is_deleted`, `created_at`, `updated_at`

#### [NEW] [create_database_orders_table.php](file:///d:/code/apps/NDHShop/database/migrations/2026_03_28_200001_create_database_orders_table.php)
Bảng đơn hàng mua/gia hạn DB (giống `gift_orders`):
- `id`, `user_id`, `cloud_database_id` (FK)
- `order_code` (ULID unique), `plan`, `amount`, `payment_method`
- `status`: `pending` / `paid` / `failed` / `refunded`
- `paid_at`, `metadata` (JSON)
- `created_at`, `updated_at`

#### [NEW] [create_api_keys_table.php](file:///d:/code/apps/NDHShop/database/migrations/2026_03_28_200002_create_api_keys_table.php)
API Key cho user truy cập qua SDK/API:
- `id`, `user_id` (FK), `name` (label)
- `key` (VARCHAR 64, UNIQUE, hashed lưu)
- `last_used_at`, `is_active` (BOOLEAN)
- `created_at`, `updated_at`

---

#### [NEW] [CloudDatabase.php](file:///d:/code/apps/NDHShop/app/Models/CloudDatabase.php)
- Extends `BaseModel`
- Constants: `ENGINE_POSTGRESQL`, `ENGINE_MYSQL`, `PLAN_FREE`, `PLAN_PRO`, `PLAN_TEAM`, `STATUS_*`, `PLAN_PRICES`, `PLAN_LIMITS`
- Relations: `user()`, `orders()`
- Methods: `isActive()`, `isFree()`, `isPostgresql()`, `isMysql()`, `getConnectionString()`, `getDefaultPort()`
- Bảng giá plan: Free (0đ, 50MB, 5 conn), Pro (50.000đ/tháng, 500MB, 20 conn), Team (150.000đ/tháng, 5GB, 50 conn)

#### [NEW] [DatabaseOrder.php](file:///d:/code/apps/NDHShop/app/Models/DatabaseOrder.php)
- Extends `Model` (không cần soft delete)
- Relations: `user()`, `cloudDatabase()`
- Method: `generateOrderCode()` (ULID)

#### [NEW] [ApiKey.php](file:///d:/code/apps/NDHShop/app/Models/ApiKey.php)
- Relations: `user()`
- Methods: `maskKey()` (chỉ hiện 8 ký tự cuối)

---

### Services — Strategy Pattern cho Multi-Engine

#### [NEW] [ProvisionerInterface.php](file:///d:/code/apps/NDHShop/app/Services/DBaaS/ProvisionerInterface.php)
Interface chuẩn cho tất cả database engine:
- `createDatabase(CloudDatabase $db): void`
- `deleteDatabase(CloudDatabase $db): void`
- `resetPassword(CloudDatabase $db, string $newPassword): void`
- `getDatabaseSize(CloudDatabase $db): float` (MB)
- `sanitizeName(string $input): string`

#### [NEW] [PostgresProvisioner.php](file:///d:/code/apps/NDHShop/app/Services/DBaaS/PostgresProvisioner.php)
Implement `ProvisionerInterface` cho PostgreSQL:
- `CREATE DATABASE` + `CREATE USER` + `GRANT ALL PRIVILEGES`
- `ALTER ROLE ... CONNECTION LIMIT` để giới hạn connection
- `pg_database_size()` để check dung lượng
- Kết nối qua `config/database.php` → `pgsql_master`

#### [NEW] [MysqlProvisioner.php](file:///d:/code/apps/NDHShop/app/Services/DBaaS/MysqlProvisioner.php)
Implement `ProvisionerInterface` cho MySQL:
- `CREATE DATABASE` + `CREATE USER` + `GRANT ALL PRIVILEGES ON db.* TO user`
- `SET GLOBAL max_user_connections` để giới hạn connection
- Query `information_schema.TABLES` để check dung lượng
- Kết nối qua `config/database.php` → `mysql_master`

#### [NEW] [DatabaseProvisioningService.php](file:///d:/code/apps/NDHShop/app/Services/DatabaseProvisioningService.php)
**Trái tim module** — Factory/Router chọn đúng provisioner theo engine:
- `getProvisioner(string $engine): ProvisionerInterface` — Trả về Postgres hoặc MySQL provisioner
- `createDatabase(CloudDatabase $db)`: Gọi provisioner tương ứng
- `deleteDatabase(CloudDatabase $db)`: Gọi provisioner tương ứng
- `sanitizeName(string $input)`: Whitelist `[a-zA-Z0-9_]`, prefix `ndh_`

#### [NEW] [DatabasePaymentService.php](file:///d:/code/apps/NDHShop/app/Services/DatabasePaymentService.php)
Xử lý thanh toán — tương tự `GiftPaymentService`:
- `processPayment(User $user, string $plan)`: Validate balance → trừ tiền → tạo order → dispatch provisioning job
- `renewDatabase(CloudDatabase $db, User $user)`: Gia hạn
- `validateBalance(User $user, int $amount)`: Kiểm tra số dư
- Sử dụng `DB::transaction()` cho atomic operation

---

### Jobs

#### [NEW] [ProvisionDatabaseJob.php](file:///d:/code/apps/NDHShop/app/Jobs/ProvisionDatabaseJob.php)
Laravel Queue Job — Async provisioning:
- Nhận `CloudDatabase` model
- Gọi `DatabaseProvisioningService::createDatabase()`
- Cập nhật status → `active` nếu thành công, `failed` nếu lỗi
- Tạo `Notification` thông báo user
- Retry 3 lần, backoff 10s

#### [NEW] [DeleteDatabaseJob.php](file:///d:/code/apps/NDHShop/app/Jobs/DeleteDatabaseJob.php)
Soft delete → Queue job xóa thật.

---

### Controllers

#### [NEW] [CloudDatabaseController.php](file:///d:/code/apps/NDHShop/app/Http/Controllers/App/CloudDatabaseController.php)
Web Dashboard cho user (authenticated):
- `index()` — Danh sách DB của user
- `create()` — Form chọn plan
- `store()` — Tạo DB mới (chọn engine PostgreSQL/MySQL, validate quota, thanh toán, dispatch job)
- `show($db)` — Chi tiết DB (connection string, status, usage)
- `destroy($db)` — Xóa DB (soft delete → job)
- `resetPassword($db)` — Reset mật khẩu DB

#### [NEW] [CloudDatabaseApiController.php](file:///d:/code/apps/NDHShop/app/Http/Controllers/Api/CloudDatabaseApiController.php)
REST API cho SDK (dùng API Key auth):
- `POST /api/v1/dbaas/databases` — Tạo DB
- `GET /api/v1/dbaas/databases` — List
- `GET /api/v1/dbaas/databases/{id}` — Detail
- `DELETE /api/v1/dbaas/databases/{id}` — Xóa
- `POST /api/v1/dbaas/databases/{id}/reset-password` — Reset pass

#### [NEW] [ApiKeyController.php](file:///d:/code/apps/NDHShop/app/Http/Controllers/App/ApiKeyController.php)
Quản lý API Key:
- `index()` — Danh sách key
- `store()` — Tạo key mới
- `destroy($key)` — Revoke key

#### [MODIFY] [admin.php](file:///d:/code/apps/NDHShop/routes/admin.php)
Thêm routes admin quản lý tất cả DB instances:
- `GET /admin/cloud-databases` — Danh sách tất cả
- `PATCH /admin/cloud-databases/{db}/suspend` — Tạm dừng
- `PATCH /admin/cloud-databases/{db}/activate` — Kích hoạt lại

---

### Routes

#### [MODIFY] [app.php](file:///d:/code/apps/NDHShop/routes/app.php)
Thêm routes cho user dashboard (authenticated):
```php
// Cloud Database (DBaaS)
Route::prefix('cloud-databases')->group(function () {
    Route::get('/', [CloudDatabaseController::class, 'index']);
    Route::get('/create', [CloudDatabaseController::class, 'create']);
    Route::post('/', [CloudDatabaseController::class, 'store']);
    Route::get('/{database:unitcode}', [CloudDatabaseController::class, 'show']);
    Route::delete('/{database:unitcode}', [CloudDatabaseController::class, 'destroy']);
    Route::post('/{database:unitcode}/reset-password', [CloudDatabaseController::class, 'resetPassword']);
});

// API Keys
Route::prefix('api-keys')->group(function () {
    Route::get('/', [ApiKeyController::class, 'index']);
    Route::post('/', [ApiKeyController::class, 'store']);
    Route::delete('/{key}', [ApiKeyController::class, 'destroy']);
});
```

#### [MODIFY] [api.php](file:///d:/code/apps/NDHShop/routes/api.php)
Thêm API endpoint cho SDK:
```php
// DBaaS API (API Key auth)
Route::middleware('auth.apikey')->prefix('v1/dbaas')->group(function () {
    Route::apiResource('databases', CloudDatabaseApiController::class);
    Route::post('databases/{id}/reset-password', ...);
});
```

---

### Config

#### [NEW] [dbaas.php](file:///d:/code/apps/NDHShop/config/dbaas.php)
```php
return [
    // Engine mặc định khi user không chọn
    'default_engine' => env('DBAAS_DEFAULT_ENGINE', 'mysql'),

    // Các engine được phép sử dụng
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

    // Bảng giá plan (áp dụng chung cho cả 2 engine)
    'plans' => [
        'free'  => ['price' => 0,      'max_db' => 2,  'max_storage_mb' => 50,   'max_connections' => 5],
        'pro'   => ['price' => 50000,  'max_db' => 10, 'max_storage_mb' => 500,  'max_connections' => 20],
        'team'  => ['price' => 150000, 'max_db' => 20, 'max_storage_mb' => 5120, 'max_connections' => 50],
    ],
];
```

#### [MODIFY] [database.php](file:///d:/code/apps/NDHShop/config/database.php)
Thêm 2 connections: `pgsql_master` (PostgreSQL) và `mysql_master` (MySQL) cho provisioning service.

---

### Blade Views

#### [NEW] Views trong `resources/views/pages/app/cloud-databases/`
| File | Mô tả |
|------|-------|
| `index.blade.php` | Danh sách DB + nút tạo mới (hiển thị badge engine: PG/MySQL) |
| `create.blade.php` | Chọn engine (PostgreSQL/MySQL) + chọn plan + xác nhận thanh toán |
| `show.blade.php` | Chi tiết DB: engine, connection string, status, dung lượng, nút copy, reset password |

#### [NEW] Views trong `resources/views/pages/app/api-keys/`
| File | Mô tả |
|------|-------|
| `index.blade.php` | Quản lý API keys |

#### [NEW] Views trong `resources/views/pages/admin/cloud-databases/`
| File | Mô tả |
|------|-------|
| `index.blade.php` | Admin: danh sách tất cả DB instances |

---

### Middleware

#### [NEW] [AuthenticateApiKey.php](file:///d:/code/apps/NDHShop/app/Http/Middleware/AuthenticateApiKey.php)
Middleware xác thực API Key cho REST API:
- Check header `X-API-Key`
- Tìm ApiKey model → attach user vào request
- Return 401 nếu key không hợp lệ

---

## Verification Plan

### Automated Tests

**Chạy migration test:**
```bash
cd d:\code\apps\NDHShop
php artisan migrate --seed
```

**Test tạo DB thủ công qua tinker** (sau khi cài PostgreSQL):
```bash
php artisan tinker
# Test sanitize
app(App\Services\DatabaseProvisioningService::class)->sanitizeName("test'; DROP TABLE--")
# Kết quả mong đợi: "ndh_test_DROP_TABLE"
```

### Manual Verification (Yêu cầu user test)

1. **Cài PostgreSQL + MySQL trên VPS**, cấu hình env `DBAAS_PGSQL_*` và `DBAAS_MYSQL_*`
2. **Đăng nhập NDHShop** → Vào `/apps/cloud-databases`
3. **Tạo DB PostgreSQL** (plan Free) → Kiểm tra:
   - Chọn engine PostgreSQL, status "Đang khởi tạo..." → "Hoạt động"
   - Connection string hiển thị đúng (port 5432)
   - Kết nối được bằng pgAdmin hoặc `psql`
4. **Tạo DB MySQL** (plan Free) → Kiểm tra:
   - Chọn engine MySQL, status tương tự
   - Connection string đúng (port 3306)
   - Kết nối được bằng MySQL Workbench hoặc `mysql` CLI
5. **Thử tạo quá quota** (Free = 2 DB) → Phải báo lỗi
6. **Reset password** → Password mới hoạt động, password cũ không dùng được
7. **Xóa DB** → Status chuyển "Đang xóa" → DB bị xóa thật trên engine tương ứng
8. **Admin panel** → `/admin/cloud-databases` hiển thị tất cả DB instances (lọc theo engine)

> [!NOTE]
> Có thể test từng engine riêng. Nếu chưa cài PostgreSQL, vẫn test được MySQL và ngược lại. Logic thanh toán/quota test được ngay cả khi chưa cài cả hai.
