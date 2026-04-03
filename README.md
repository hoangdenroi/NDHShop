# NDHShop - Nền tảng Mua và Quản lý VPS

NDHShop là một nền tảng thương mại điện tử chuyên cung cấp dịch vụ mua bán và quản lý VPS (Virtual Private Server). Hệ thống được thiết kế tối ưu, giúp người dùng dễ dàng lựa chọn cấu hình, thanh toán và quản lý các máy chủ ảo của mình trên một giao diện hợp nhất và trực quan.

## Môi trường hoạt động & Định hướng phát triển

Ở giai đoạn đầu, NDHShop tập trung tích hợp và tự động hóa với nhà cung cấp **Hetzner** nhằm mang đến trải nghiệm chất lượng cao và mức giá hợp lý. Tuy nhiên, kiến trúc hệ thống được xây dựng linh hoạt, sẵn sàng hỗ trợ các nhà cung cấp đám mây lớn khác như **DigitalOcean**, **Vultr** và **Linode** trong tương lai để đáp ứng đa dạng yêu cầu của khách hàng.

---

## 🎯 Danh sách tính năng (Features)

Hệ thống cung cấp các nhóm chức năng chính bao gồm:

### Dành cho Khách hàng (User Features)
- **Tài khoản & Số dư:** Đăng ký, đăng nhập an toàn, nạp tiền vào tài khoản để thanh toán dịch vụ.
- **Mua sắm VPS:** Chọn gói cấu hình, hệ điều hành mong muốn và tạo đơn hàng.
- **Quản lý VPS:**
  - Khởi động, Tắt, Khởi động lại máy chủ.
  - Quản lý SSH Keys.
  - Cài lại hệ điều hành (Reinstall OS).
  - Thống kê tình trạng hoạt động.

### Dành cho Quản trị viên (Admin Features)
- **Quản lý sản phẩm:** Tạo mới và cấu hình các gói VPS, tùy chỉnh cấu hình chi tiết (CPU, RAM, Disk).
- **Quản lý đơn hàng:** Duyệt/hủy đơn hàng, hỗ trợ kích hoạt thủ công cho các máy chủ không hỗ trợ API.
- **Quản lý người dùng, giao dịch:** Kiểm soát dòng tiền, trạng thái giao dịch và lịch sử hoạt động.

---

## 🚀 TODO / Roadmap tích hợp API

Kế hoạch phát triển và tự động hóa hệ thống sẽ trải qua các giai đoạn sau:

- [x] Xây dựng UI/UX và thiết lập cơ sở dữ liệu cốt lõi (Core Database & UI).
- [x] Tạo lập hệ thống quản lý giao dịch và số dư nội bộ.
- [ ] **Planned Hetzner Integration:** Tích hợp API của Hetzner Cloud cho bước khởi tạo ban đầu.
- [ ] **Provision VPS automatically:** Triển khai luồng tự động tạo (provisioning) máy chủ trên Hetzner ngay khi người dùng thanh toán đơn hàng thành công.
- [ ] **Manage power state, SSH keys, reinstall OS:** Tích hợp quản trị vòng đời (Power On/Off, Reset, thay đổi OS ban đầu) thông qua API Hetzner trực tiếp trên Dashboard của trang web.
- [ ] Mở rộng kiến trúc chuẩn bị hỗ trợ Vultr & DigitalOcean.
- [ ] Triển khai các API kết nối Vultr và DigitalOcean.

---

## 📁 Cấu trúc Project (Project Structure)

Dự án được phát triển dựa trên framework Laravel với cấu trúc tiêu chuẩn, cùng với một số thành phần tùy chỉnh cho nghiệp vụ (Business Logic):

```text
NDHShop/
├── app/
│   ├── Http/
│   │   ├── Controllers/   # Xử lý logic API và Web
│   │   └── Requests/      # Validate thông tin đầu vào
│   ├── Models/            # Model cơ sở dữ liệu (User, Order, VpsPackage, Transaction...)
│   └── Services/          # Dịch vụ backend API (HetznerService...)
├── database/
│   ├── migrations/        # Cấu trúc các bảng CSDL
│   └── seeders/           # Dữ liệu khởi tạo mặc định
├── resources/
│   ├── views/             # Giao diện Blade templating (Admin & App panel)
│   ├── css/               # Tailwind CSS / Styles
│   └── js/                # Javascript / Alpine.js
├── routes/
│   ├── web.php          
│   ├── api.php
│   ├── admin.php          # Route dành riêng cho khu vực quản trị
│   └── app.php            # Route ứng dụng dành cho người dùng 
└── tests/                 # Unit & Feature tests hệ thống
```

---

*Dự án đảm bảo các tiêu chuẩn bảo mật cơ bản như phòng chống XSS, SQL Injection và tuân thủ các quy tắc mã nguồn chuẩn mực.*
