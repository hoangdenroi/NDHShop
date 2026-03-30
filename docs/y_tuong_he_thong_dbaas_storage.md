# 🚀 Ý Tưởng Chi Tiết: Hệ Thống Mini DBaaS & S3-like Storage

> Tổng hợp từ cuộc trao đổi trên ChatGPT, kết hợp phân tích và đề xuất bổ sung từ góc nhìn Tech Lead.

---

## 1. Tổng Quan Sản Phẩm

### Mục tiêu
Xây dựng nền tảng **SaaS** cho phép developer:
- **Khởi tạo nhanh** database PostgreSQL chỉ qua vài click hoặc API call
- **Lưu trữ file** tương tự S3 (upload, quản lý, serve qua CDN mini)
- Quản lý toàn bộ qua **Web Dashboard** + **REST API**

### Đối tượng sử dụng
- Developer cá nhân / freelancer cần DB nhanh cho side project
- Startup nhỏ cần hạ tầng DB không muốn tự quản lý
- Team dev nội bộ cần môi trường dev/staging nhanh

---

## 2. Kiến Trúc Tổng Thể

### A. Mini DBaaS — Database as a Service

```
┌─────────────────────────────────────────────────────┐
│                    USERS                             │
│         (Web Dashboard / API Client / SDK)           │
└─────────────────┬───────────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────────┐
│            WEB PORTAL (Blade / Alpine.js)            │
│    Hoặc Angular (cho dashboard riêng biệt)          │
└─────────────────┬───────────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────────┐
│          CONTROL PLANE API (Spring Boot)             │
│  ┌──────────┐ ┌──────────┐ ┌──────────────────────┐ │
│  │Auth (JWT)│ │Billing   │ │Database Management   │ │
│  └──────────┘ └──────────┘ └──────────────────────┘ │
└─────────────────┬───────────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────────┐
│         PROVISIONING SERVICE (Queue-based)           │
│  ┌──────────────────┐  ┌──────────────────────────┐ │
│  │Tạo DB / User     │  │Giới hạn resource (quota) │ │
│  │(Async via Queue)  │  │Connection Pooling        │ │
│  └──────────────────┘  └──────────────────────────┘ │
└─────────────────┬───────────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────────┐
│         POSTGRESQL CORE INSTANCE (Master)            │
│  ├── db_user_001                                     │
│  ├── db_user_002                                     │
│  ├── db_user_003                                     │
│  └── ...                                             │
└─────────────────────────────────────────────────────┘
```

> [!IMPORTANT]
> **Giai đoạn đầu**: Dùng **Shared Instance** (1 PostgreSQL cluster lớn, phân quyền bằng User PostgreSQL) để tiết kiệm RAM. Sau này nâng cấp: `Shared Instance → Multi Node → Per Customer Instance`.

---

### B. S3-like Storage

```
Browser ──▶ Laravel Backend ──▶ Local File System (Storage)
                                      │
                               Nginx (CDN mini - serve static file)
```

**Cấu trúc dữ liệu**: Quản lý theo `Buckets` (thùng chứa) và `Objects` (file).

---

## 3. Database Schema Chi Tiết (Control Plane)

Database quản trị riêng: `dbaas_control`

### Bảng `users`
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | UUID (PK) | ID duy nhất |
| `email` | VARCHAR(255) UNIQUE | Email đăng ký |
| `password_hash` | TEXT | Mật khẩu mã hóa |
| `api_key` | VARCHAR(64) UNIQUE | API Key cho SDK |
| `plan` | VARCHAR(20) DEFAULT 'free' | Gói dịch vụ |
| `max_databases` | INT DEFAULT 2 | Giới hạn số DB |
| `max_storage_mb` | INT DEFAULT 100 | Giới hạn dung lượng |
| `created_at` | TIMESTAMP | Ngày tạo |

### Bảng `databases`
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | UUID (PK) | ID duy nhất |
| `user_id` | UUID (FK → users) | Chủ sở hữu |
| `db_name` | VARCHAR(100) | Tên DB thực tế |
| `db_user` | VARCHAR(100) | Username PostgreSQL |
| `password_hash` | TEXT | Mật khẩu DB (mã hóa) |
| `host` | VARCHAR(255) | Host kết nối |
| `port` | INT DEFAULT 5432 | Port |
| `status` | VARCHAR(20) | `PROVISIONING` / `ACTIVE` / `SUSPENDED` / `DELETING` |
| `max_connections` | INT DEFAULT 10 | Giới hạn connection |
| `storage_used_mb` | DECIMAL | Dung lượng đã dùng |
| `created_at` | TIMESTAMP | Ngày tạo |

### Bảng `usage_logs`
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | UUID (PK) | ID duy nhất |
| `database_id` | UUID (FK → databases) | DB liên quan |
| `action` | VARCHAR(50) | Loại hành động |
| `created_at` | TIMESTAMP | Thời gian |

### Bảng `buckets` (Storage)
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | UUID (PK) | ID duy nhất |
| `user_id` | UUID (FK → users) | Chủ sở hữu |
| `name` | VARCHAR(100) | Tên bucket |
| `is_public` | BOOLEAN DEFAULT false | Cho phép truy cập công khai |
| `storage_used_mb` | DECIMAL | Dung lượng đã dùng |
| `created_at` | TIMESTAMP | Ngày tạo |

### Bảng `objects` (File trong bucket)
| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | UUID (PK) | ID duy nhất |
| `bucket_id` | UUID (FK → buckets) | Bucket chứa |
| `key` | VARCHAR(500) | Đường dẫn file (VD: `images/logo.png`) |
| `mime_type` | VARCHAR(100) | Loại file |
| `size_bytes` | BIGINT | Kích thước file |
| `checksum` | VARCHAR(64) | Hash kiểm tra toàn vẹn |
| `created_at` | TIMESTAMP | Ngày tạo |

---

## 4. API Endpoints Chuẩn SaaS

### 🗄️ Database Management

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| `POST` | `/api/databases` | Tạo database mới |
| `GET` | `/api/databases` | Danh sách DB của user |
| `GET` | `/api/databases/{id}` | Chi tiết 1 DB |
| `DELETE` | `/api/databases/{id}` | Xóa DB (soft delete → queue job xóa thật) |
| `POST` | `/api/databases/{id}/reset-password` | Reset mật khẩu DB |

**Ví dụ Response khi tạo DB:**
```json
{
  "id": "uuid-xxx",
  "host": "db.yourapp.com",
  "port": 5432,
  "database": "db_user_001",
  "username": "user_001",
  "password": "random_generated_pass",
  "connection_string": "postgresql://user_001:pass@db.yourapp.com:5432/db_user_001",
  "status": "PROVISIONING"
}
```

### 📦 Storage Management

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| `POST` | `/api/buckets` | Tạo bucket |
| `GET` | `/api/buckets` | Danh sách bucket |
| `POST` | `/api/buckets/{id}/objects` | Upload file |
| `GET` | `/api/buckets/{id}/objects` | Danh sách file |
| `DELETE` | `/api/buckets/{id}/objects/{key}` | Xóa file |

---

## 5. Provisioning Service — Trái Tim Hệ Thống

### Flow tạo DB (Async)

```
API nhận request
  → Lưu metadata status = "PROVISIONING"
  → Trả response ngay (202 Accepted)
  → Đẩy vào Queue
      → Worker nhận job
      → CREATE DATABASE, CREATE USER, GRANT PRIVILEGES
      → Cập nhật status = "ACTIVE"
      → Gửi notification (webhook / email)
```

> [!TIP]
> Async Provisioning rất quan trọng: tránh UI bị lag khi nhiều user tạo DB cùng lúc, và cho phép retry nếu tạo thất bại.

### Flow xóa DB (Soft Delete)

```
API nhận DELETE request
  → Cập nhật status = "DELETING"
  → Đẩy vào Queue
      → Worker: DROP DATABASE, DROP USER
      → Xóa metadata
```

---

## 6. Bảo Mật — Quyết Định Sống Còn

### Chống SQL Injection khi tạo DB

> [!CAUTION]
> **KHÔNG BAO GIỜ** nhận raw `dbName` từ user rồi nối chuỗi trực tiếp! Luôn sanitize bằng whitelist ký tự `[a-zA-Z0-9_]`.

```java
// ✅ Đúng
String dbName = sanitize("db_" + customerId);

// ❌ Sai - SQL Injection risk
jdbcTemplate.execute("CREATE DATABASE " + userInput);
```

### Giới hạn Connection
```sql
ALTER ROLE user_001 CONNECTION LIMIT 10;
```

### Read-only Plan
```sql
GRANT CONNECT ON DATABASE db_user TO readonly_user;
GRANT USAGE ON SCHEMA public TO readonly_user;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO readonly_user;
```

### Bảo mật Storage
- **Chặn Path Traversal**: Filter ký tự `../` trong tên file/đường dẫn
- **Validate Mime-type**: Chỉ cho phép loại file đã đăng ký
- **Giới hạn dung lượng**: Theo Quota của user plan
- **Không expose IP DB trực tiếp**: Dùng Nginx reverse proxy

---

## 7. Billing & Pricing Plans

| Tính năng | Free | Pro | Team |
|-----------|------|-----|------|
| Số lượng DB | 2 | 10 | 20 |
| Dung lượng/DB | 50MB | 500MB | 5GB |
| Connection limit | 5 | 20 | 50 |
| Backup | ❌ | Daily | Daily |
| Readonly accounts | ❌ | ❌ | ✅ |
| Restore snapshots | ❌ | ❌ | ✅ |
| Query analytics | ❌ | ❌ | ✅ |
| Giá | Miễn phí | ~$5/tháng | ~$15/tháng |

**Tích hợp thanh toán**: Stripe hoặc PayPal.

> [!IMPORTANT]
> Thiết lập Quota ngay từ đầu để tránh bị lạm dụng tài nguyên. Free plan cần giới hạn nghiêm ngặt.

---

## 8. Monitoring — Không Có Là "Toang"

### Metrics cần thu thập
- Số lượng DB đã tạo
- Active connections (theo từng user)
- Slow queries
- Disk usage per customer
- CPU usage
- DB size

### Stack khuyến nghị
- **Prometheus**: Thu thập metrics
- **Grafana**: Dashboard trực quan
- **PostgreSQL Exporter**: Export metrics từ PostgreSQL

---

## 9. Tech Stack Đề Xuất

| Thành phần | Công nghệ | Lý do |
|------------|-----------|-------|
| **Control Plane API** | Spring Boot (Java) | Bạn đang học Spring Boot JWT, phù hợp |
| **Web Portal** | Blade + Alpine.js hoặc Angular | Tận dụng kinh nghiệm có sẵn |
| **Database Engine** | PostgreSQL | Mature, mạnh mẽ, hỗ trợ multi-tenant tốt |
| **Connection Pooling** | PgBouncer | Giới hạn resource hiệu quả |
| **Queue** | RabbitMQ hoặc Redis Queue | Async provisioning |
| **CDN/File Serving** | Nginx | Serve file tĩnh hiệu quả |
| **Monitoring** | Prometheus + Grafana | Industry standard |
| **Auth** | JWT (Spring Security) | Stateless, phù hợp API-first |

### Cấu trúc Module Spring Boot

```
src/main/java/com/dbaas/
├── auth/          # JWT authentication
├── billing/       # Quản lý plan, quota
├── database/      # CRUD database instances  
├── provisioning/  # Tạo/xóa DB thực tế trên PostgreSQL
├── monitoring/    # Thu thập metrics
└── storage/       # Module lưu trữ file (Phase 2)
```

---

## 10. Roadmap Triển Khai (14+ Ngày)

### Tuần 1: Core & API
- [ ] Cài đặt PostgreSQL local
- [ ] Spring Boot auth JWT
- [ ] API tạo/xóa DB (`POST /api/databases`, `DELETE /api/databases/{id}`)
- [ ] Bảng metadata (`users`, `databases`, `usage_logs`)
- [ ] Provisioning Service (tạo DB/User tự động trên PostgreSQL)
- [ ] SDK đơn giản (PHP/JS) để tích hợp

### Tuần 2: Portal & Billing  
- [ ] Web Dashboard (quản lý DB, xem connection string)
- [ ] Tích hợp Auth (đăng ký/đăng nhập)
- [ ] Billing plan (Free vs Pro quotas)
- [ ] Angular portal hoặc Blade + Alpine.js

### Tuần 3: Storage & Monitoring (Mở rộng)
- [ ] Module Storage (Buckets + Objects)
- [ ] Upload/download file qua API
- [ ] Nginx CDN mini cho static file
- [ ] Prometheus + Grafana monitoring
- [ ] Alert khi resource gần hết quota

---

## 11. Lời Khuyên Chiến Lược

> [!NOTE]
> ### Nguyên tắc MVP
> 1. **Đừng Over-engineer**: Dùng chung 1 PostgreSQL cluster, phân quyền bằng User — tiết kiệm RAM, đủ cho 100+ user đầu tiên
> 2. **API-First**: Xây API tạo DB chuẩn trước, UI làm đơn giản sau
> 3. **Billing sớm**: Thiết lập Quota (Free = 2 DB, 50MB) ngay từ đầu
> 4. **Async mọi thứ**: Provisioning qua Queue, delete qua Queue
> 5. **Security không thỏa hiệp**: Sanitize input, connection limit, không expose PostgreSQL port ra ngoài

---

## 12. Câu Hỏi Cần Cân Nhắc Trước Khi Bắt Tay

1. **Dự án này sẽ là module mới trong NDHShop hay là project riêng hoàn toàn?**
2. **Backend chính dùng Spring Boot (Java) hay Laravel (PHP)?** — Cuộc chat GPT gợi ý Spring Boot vì bạn đang học, nhưng NDHShop hiện tại là Laravel.
3. **Hosting ở đâu?** VPS riêng hay cloud provider? Ảnh hưởng đến cách setup PostgreSQL và Nginx.
4. **Ưu tiên build module nào trước?** DBaaS hay Storage?
5. **Tích hợp thanh toán qua cổng nào?** Stripe, PayPal, hay cổng thanh toán Việt Nam (MoMo, VNPay)?
