# NDHShop - Nền tảng Thương mại Điện tử và Quản trị VPS 🚀

NDHShop là một nền tảng thương mại điện tử chuyên cung cấp dịch vụ phân phối và quản lý VPS (Virtual Private Server) hoàn toàn tự động. Hệ thống được xây dựng với mục tiêu mang đến trải nghiệm người dùng (UX) tuyệt vời, mượt mà như một ứng dụng độc lập (SPA), tích hợp sâu với các nhà cung cấp đám mây hàng đầu để tự động hóa hoàn toàn quy trình cung cấp dịch vụ máy chủ và cơ sở dữ liệu.

## 🛠 Công nghệ Cốt lõi (Tech Stack)

Dự án được phát triển theo kiến trúc hiện đại, tận dụng sức mạnh của hệ sinh thái TALL (nhưng được thiết kế riêng biệt để tối ưu UI/UX):

- **Backend:** PHP 8.2+ & **Laravel 12.0**
- **Frontend Framework:** **Alpine.js 3** (Xử lý DOM, trạng thái cục bộ) & **Vite**
- **Giao diện (Styling):** **TailwindCSS 4.0** (Hỗ trợ Dark Mode, thiết kế theo hướng Component-based, thẩm mỹ cực kỳ cao)
- **Kiến trúc SPA:** **@hotwired/turbo** (Điều hướng trang Single Page Application không cần tải lại trang, hiển thị tức thì)
- **Cơ sở dữ liệu:** PostgreSQL
- **Tích hợp API Đám mây:** Tích hợp sâu API của **Hetzner Cloud** (Sẵn sàng mở rộng cho Vultr, DigitalOcean)

---

## 🌟 Chức năng Nổi bật (Key Features)

Hệ thống được chia thành nhiều Module chuyên biệt và tinh vi, bao gồm:

### 1. Quản trị VPS Toàn diện (Hetzner Cloud Integration)

- **Tự động khởi tạo (Automated Provisioning):** Máy chủ được tự động tạo và bàn giao cho khách hàng ngay lập tức sau khi thanh toán thành công.
- **Bảng điều khiển trực quan:** Giao diện quản lý từng VPS được chia Tab khoa học (Thông tin, Kết nối, Điều khiển, Bảo mật, Cài đặt).
- **Hành động Vòng đời Server:** Hỗ trợ khởi động, tắt, khởi động lại, và **Cài đặt lại Hệ điều hành (Reinstall OS)** với giao diện chọn OS dạng Grid thông minh.
- **Quản lý Kết nối & SSH:** Quản lý SSH Key tiện lợi, hiển thị hướng dẫn truy cập rõ ràng (như hiển thị Port 22 mặc định đi kèm sao chép một chạm) nhằm hỗ trợ người dùng mới dễ dàng tương tác.
- **Nhật ký hoạt động (Activity Logs):** Quản lý trạng thái và nhật ký máy chủ với tính năng phân trang tại máy khách (Client-side) siều mượt thông qua Alpine.js.

### 2. Auto-Provisioning Cơ sở dữ liệu (Database Provisioning)

- Hỗ trợ triển khai và cấp quyền khởi tạo tự động siêu tốc cho cho cả **MySQL** và **PostgreSQL** ngay trên web của người dùng.
- **Bảo mật phân tầng độc lập:** Sử dụng cấu trúc `DO` blocks an toàn, cấp quyền Idempotent và giới hạn quyền (Role Isolation) tuyệt đối. Đảm bảo mỗi cơ sở dữ liệu và người dùng hoạt động tách biệt, không can thiệp chéo hay xung đột.

### 3. Trải nghiệm UI/UX Cực đỉnh, Không gián đoạn (SPA & Background Music)

- **Trải nghiệm SPA mượt mà:** Khai thác _@hotwired/turbo_, cho phép chuyển đổi Tab hay Route cực nhanh chỉ trong vài mili-giây mà không hề xuất hiện hiệu ứng chớp trang trắng.
  <!-- - **Trình phát nhạc & Floating Player (Turbo Permanent):** Trình phát âm thanh hoặc YouTube vẫn tiếp tục tiếp diễn mượt mà khi khách hàng chuyển hướng qua các trang mà không bị ngắt quãng. -->
- **Giao diện Hùng vĩ, lôi cuốn:** Hỗ trợ Floating Action Buttons (FAB), các bảng điều khiển hiệu ứng mờ (Glassmorphism), màu sắc theo dải sắc thái bắt mắt, sang trọng.

### 4. Hệ thống Chia sẻ Quà tặng & Mã QR Nghệ thuật (Gift & QR System)

- Khởi tạo mã giảm giá (Coupon Code), quản lý danh sách và linh hoạt gán tiêu đề tự chọn cho các quà tặng.
- **Trình Sinh mã QR Nâng cao (Advanced QR Renderer):** Được tích hợp thẳng vào Frontend, cho phép tuỳ biến mã QR không giới hạn với những tuỳ chọn hình dạng độc đáo như: Hình Trái tim, Lục giác, Vòng tròn.
- Có khả năng chèn **Ảnh nền tùy biến** sau QR, xử lý Pattern Filler thông minh nhằm giữ độ quét chính xác và cho phép xuất ấn bản chất lượng (PNG/SVG).

### 5. Bảng theo dõi Sức khỏe Hệ thống (Server Monitoring Dashboard)

- Dành riêng cho Admin, sở hữu bảng giám sát đo lường sức mạnh trung tâm cực kì nhanh nhạy.
- Cập nhật số liệu thời gian thực cho Tải CPU, Mức sử dụng Bộ nhớ (RAM), Dung lượng Ổ cứng (Disk) và Lưu lượng Mạng (Network Data) được thúc đẩy qua Alpine.js.

---

## 📁 Cấu trúc Dự án (Project Structure)

Dự án tuân theo Design Pattern đặc trưng của Laravel với một số cải tiến nhằm cô lập xử lý Logic vào các tầng riêng biệt của kiến trúc MVP/MVC:

```text
NDHShop/
├── app/
│   ├── Http/
│   │   ├── Controllers/   # Phụ trách điều hướng Request / Tương tác API và Web
│   │   └── Requests/      # Validate Form cho Request Data siêu an toàn
│   ├── Models/            # Lớp Eloquent ORM
│   └── Services/          # Business Layer xử lý logic chuyên sâu (HetznerService, Provisioning, v.v.)
├── database/
│   ├── migrations/        # Định nghĩa lược đồ dữ liệu (Schema) và được làm gọn (Squashed) liên tục
│   └── seeders/           # Sinh dữ liệu mẫu thử nghiệm
├── resources/
│   ├── views/             # Giao diện hiển thị được viết bằng Blade (kết hợp Alpine/Triggers)
│   ├── css/               # Cấu hình TailwindCSS Version 4 / Chỉ thị CSS chuyên biệt
│   └── js/                # File khởi tạo Alpine components, Turbo handlers và Vite entry
├── routes/
│   ├── web.php            # Tương tác và hiển thị public
│   ├── api.php            # Nơi phát đi API chung
│   ├── admin.php          # Route Group bảo mật chỉ định cho Admin Dashboard
│   └── app.php            # Route Group cá nhân cho trang quản trị của người dùng
└── tests/                 # Unit & Feature testing của Ứng dụng
```

---

## 🚀 Lộ trình Tích hợp và Mở rộng (Roadmap)

- [x] Cài đặt **Turbo** tạo trải nghiệm SPA đột phá cho toàn hệ thống.
- [x] Xây dựng UI thông minh, quản lý trạng thái nghe nhìn xuyên tuyến (Persistent Player).
- [x] Khép kín chu trình tự động cài đặt hệ điều hành với API Hetzner Cloud.
- [x] Hoàn thiện cấu hình tạo tự động Database (MySQL/PostgeSQL) siêu an toàn.
- [ ] Tích hợp API của **Vultr** & **DigitalOcean** làm lựa chọn thay thế hoặc cao cấp.
- [ ] Cập nhật cổng thanh toán Local và Crypto (USDT).

## 🛡 Đặc điểm Nhấn mạnh Về Bảo mật

1. Tuân thủ chống 100% tấn công tiêm nhiễm SQL (SQL Injection) và XSS (sử dụng Blade escape mặc định).
2. Tác vụ API của Cloud được mã hoá cấu hình với Token Environment ẩn.
3. Chặn quyền truy xuất dữ liệu ngoài nhóm thông qua Role based Middleware vững mạnh.
