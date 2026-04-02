# Kế Hoạch Xây Dựng DBaaS Monitoring (Cron Job)

Tài liệu này lưu trữ lại giải pháp kỹ thuật để giám sát thời gian hoạt động (last_activity_at) và dung lượng của các Node Database (MySQL/PostgreSQL) cho module Mini DBaaS trong ứng dụng NDHShop.

## 1. Vấn Đề Hiện Tại
- Khi User kết nối trực tiếp vào DB Instances, kết nối diễn ra qua giao thức TCP (PORT 3306/5432) đi thẳng tới Database Daemon.
- Ứng dụng Laravel không đóng vai trò Proxy nên bị "mù", không thể nhận biết thức thời khi User connect/disconnect.
- Trạng thái `last_activity_at` trên giao diện hiện tại đang nằm yên ở "Chưa có".

## 2. Giải Pháp: Background Polling Job
Sử dụng Laravel Task Scheduler để định kỳ gửi query thăm dò nội bộ máy chủ Database nhằm lấy ra con số biến động về dung lượng và connect.

### Thiết Kế Cron Job
Tạo Command: `php artisan dbaas:monitor-activity` và setup chạy mỗi 5 phút `->everyFiveMinutes()`.

### Truy Vấn Trích Xuất Dữ Liệu Activity
Sử dụng tài khoản Root (Admin) thọc sâu vào tầng hệ thống để lấy số liệu của từng User mà không cần mật khẩu của họ.

**Với MySQL (MariaDB):**
Quét bảng Performance Schema để đếm tổng số connection.
```sql
SELECT USER, TOTAL_CONNECTIONS 
FROM performance_schema.accounts 
WHERE USER LIKE 'ndh_%';
```
*Logic*: Khi `TOTAL_CONNECTIONS` thay đổi (lớn hơn số tĩnh trong lần scan trước), báo hiệu có sự kiện Connect mới trong 5 phút qua ➡ Cập nhật `last_activity_at = now()`.

**Với PostgreSQL:**
Đếm số transactions đã commit hoặc số tuples thay đổi.
```sql
SELECT datname, xact_commit, tup_inserted 
FROM pg_stat_database
WHERE datname LIKE 'ndh_%';
```
*Logic*: Tương tự, nếu thông số nhảy cóc, báo hiệu có Activity.

## 3. Cập Nhật Mức Sức Dụng Dung Lượng (Storage Monitoring)
Bên cạnh tracking connection, Job này cũng sẽ gánh luôn trọng trách quét dung lượng DB.

**MySQL:**
```sql
SELECT table_schema AS 'db_name', 
ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' 
FROM information_schema.tables 
WHERE table_schema LIKE 'ndh_%'
GROUP BY table_schema;
```

**PostgreSQL:**
```sql
SELECT datname AS db_name,
ROUND(pg_database_size(datname) / 1024 / 1024, 2) AS size_mb
FROM pg_database
WHERE datname LIKE 'ndh_%';
```

## 4. Hành Động Theo Hệ Quả (Pruning / Suspend)
Sau khi đồng bộ dữ liệu từ Polling, Laravel sẽ chạy tiếp một vòng quét nội bộ:
- Giọng DB nào tràn dung lượng (`storage_used_mb > max_storage_mb`): Update status thành `suspended` và dùng PDO thu hồi quyền INSERT/CREATE cục bộ.
- DB gói Free mà `last_activity_at` vượt quá N ngày không có thay đổi (vd 14 ngày): Update status thành `suspended` và revoke quyền kết nối.
