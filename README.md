# Website quảng bá sản phẩm bằng WordPress

## 1. Thông tin sinh viên

- Họ và tên: Lê Phương Mai
- Mã số sinh viên: DTC245160023
- Lớp: KHMT K23A
- Môn học: Triển khai và Quản trị Hệ thống Phần mềm
- Đề tài: Website Quảng bá Sản phẩm (WordPress)
- Giảng viên hướng dẫn: Hoàng Văn Tâm

## 2. Giới thiệu đề tài

TechShow là website giới thiệu các sản phẩm công nghệ như tai nghe, bàn phím, đồng hồ thông minh và một số thiết bị khác. Website sử dụng WordPress để quản lý nội dung và MySQL để lưu trữ dữ liệu.

Ngoài phần giao diện, đề tài còn triển khai Nginx làm Reverse Proxy, công cụ quản lý cơ sở dữ liệu phpMyAdmin, hệ thống giám sát Prometheus + Grafana và thu thập log tập trung Loki + Promtail. Toàn bộ các dịch vụ được đóng gói và vận hành bằng Docker Compose trên máy ảo Ubuntu Server trong môi trường VMware Workstation.

## 3. Các chức năng chính

- Hiển thị trang chủ và danh sách các sản phẩm công nghệ nổi bật.
- Xem danh mục sản phẩm và phân loại theo nhóm.
- Tìm kiếm sản phẩm theo từ khóa.
- Xem trang thông tin chi tiết của từng sản phẩm kèm thông số kỹ thuật.
- Xem trang giới thiệu thương hiệu và thông tin liên hệ.
- Quản trị nội dung: thêm, sửa, xóa sản phẩm trong bảng quản trị WordPress (`wp-admin`).
- Tích hợp sẵn 6 sản phẩm mẫu cùng hình ảnh vector minh họa.

## 4. Công nghệ sử dụng

| Thành phần | Mục đích sử dụng |
|---|---|
| VMware Workstation | Môi trường ảo hóa tạo máy ảo máy chủ |
| Ubuntu Server 24.04 LTS | Hệ điều hành máy chủ trên máy ảo |
| Docker & Docker Compose | Quản lý đóng gói và khởi chạy các dịch vụ container |
| WordPress & PHP-FPM | Xây dựng website và quản trị nội dung sản phẩm |
| MySQL 8.4 | Hệ quản trị cơ sở dữ liệu lưu trữ dữ liệu WordPress |
| phpMyAdmin | Giao diện web trực quan quản lý cơ sở dữ liệu |
| Nginx Reverse Proxy | Tiếp nhận request người dùng, phục vụ file tĩnh và chuyển tiếp xử lý PHP tới WordPress |
| Prometheus | Thu thập số liệu giám sát (metrics) định kỳ từ các exporter |
| Grafana | Trực quan hóa số liệu qua Dashboard và hỗ trợ tra cứu log |
| cAdvisor, Nginx exporter, MySQL exporter | Cung cấp metrics chi tiết về container, web server và cơ sở dữ liệu |
| Loki & Promtail | Thu thập log container, log truy cập Nginx và truy vấn tập trung bằng LogQL |

Luồng truy cập: **Trình duyệt → Nginx Reverse Proxy (cổng 8080) → WordPress PHP-FPM (cổng 9000) → MySQL Database (cổng 3306)**.

## 5. Cấu trúc thư mục

```text
productshowcase/
├── .env.example                     # File mẫu biến môi trường cấu hình cổng, mật khẩu
├── .gitignore                       # Loại trừ file môi trường .env và dữ liệu tạm
├── README.md                        # Hướng dẫn chi tiết triển khai và quản trị hệ thống
├── compose.yaml                     # File cấu hình chạy toàn bộ dịch vụ Docker Compose
├── config/
│   ├── grafana/
│   │   ├── dashboards/
│   │   │   └── overview.json       # Dashboard giám sát mẫu tích hợp sẵn
│   │   └── provisioning/
│   │       ├── dashboards/
│   │       │   └── default.yml      # Cấu hình tự nạp dashboard cho Grafana
│   │       └── datasources/
│   │           └── default.yml      # Cấu hình tự nạp nguồn dữ liệu Prometheus & Loki
│   ├── loki/
│   │   └── config.yml               # Cấu hình hệ thống lưu trữ log Loki
│   ├── nginx/
│   │   └── default.conf             # Cấu hình Nginx reverse proxy, security headers
│   ├── prometheus/
│   │   └── prometheus.yml           # Cấu hình scrape metrics các target định kỳ
│   └── promtail/
│       └── config.yml               # Cấu hình thu thập log Docker và log Nginx gửi về Loki
└── theme/
    └── techshow/                    # Mã nguồn theme WordPress TechShow tùy biến riêng
        ├── archive-product.php      # Giao diện danh sách sản phẩm
        ├── assets/                  # Các hình ảnh minh họa vector (SVG) sản phẩm
        ├── footer.php               # Chân trang website
        ├── front-page.php           # Giao diện trang chủ giới thiệu sản phẩm
        ├── functions.php            # Khai báo Custom Post Type và tự động nạp dữ liệu mẫu
        ├── header.php               # Đầu trang và thanh điều hướng
        ├── index.php                # File giao diện dự phòng
        ├── single-product.php       # Giao diện chi tiết sản phẩm
        └── style.css                # Định kiểu phong cách hiện đại cho website
```

## 6. Chuẩn bị môi trường

Cấu hình máy ảo đề xuất trên VMware Workstation:

- **Hệ điều hành**: Ubuntu Server 24.04 LTS (x86_64).
- **Phần cứng**: 4 vCPU, 8 GB RAM, 50 GB ổ cứng.
- **Card mạng**: NAT mode, có kết nối Internet để kéo Docker image.

## 7. Hướng dẫn triển khai hệ thống

### Bước 1: Sao chép file cấu hình môi trường

Trên máy ảo Ubuntu, tại thư mục `productshowcase`:

```bash
cp .env.example .env
```

Bạn có thể chỉnh sửa file `.env` nếu muốn thay đổi cổng truy cập hoặc mật khẩu theo ý muốn.

### Bước 2: Khởi động các dịch vụ

Khởi chạy toàn bộ hệ thống bằng Docker Compose:

```bash
docker compose pull
docker compose up -d
docker compose ps
```

Đợi các container chuyển sang trạng thái `healthy` (hoặc `running`).

### Bước 3: Truy cập website và kích hoạt dữ liệu mẫu

Mở trình duyệt trên máy thật và truy cập vào địa chỉ IP của máy ảo:

```text
http://IP_VM:8080/
```

- Trong lần đầu truy cập, hoàn tất các bước thiết lập cơ bản của WordPress (chọn ngôn ngữ, đặt tên website, tạo tài khoản quản trị).
- Truy cập trang quản trị tại `http://IP_VM:8080/wp-admin/` và kích hoạt theme **TechShow**.
- Theme đã được lập trình sẵn cơ chế tự động khởi tạo **6 sản phẩm mẫu** kèm ảnh minh họa và các trang Giới thiệu, Liên hệ ngay khi kích hoạt.

### Bước 4: Truy cập các công cụ quản trị qua SSH Tunnel

Để bảo mật theo tiêu chí Hardening, các công cụ quản trị (phpMyAdmin, Grafana, Prometheus) chỉ mở cổng trên `127.0.0.1` của máy ảo. Trên máy Windows, mở PowerShell và chạy lệnh SSH Tunnel:

```powershell
ssh -N -L 13000:127.0.0.1:3000 -L 19090:127.0.0.1:9090 -L 18081:127.0.0.1:8081 student@IP_VM
```

*(Thay `student` bằng tên user trên Ubuntu và `IP_VM` bằng IP của máy ảo).*

Sau khi mở tunnel, truy cập từ máy thật Windows:

| Công cụ | Địa chỉ trên Windows | Tài khoản mặc định | Mật khẩu |
|---|---|---|---|
| **phpMyAdmin** | `http://127.0.0.1:18081` | `wpapp` | Lấy trong `.env` (`MYSQL_PASSWORD`) |
| **Grafana** | `http://127.0.0.1:13000` | `admin` | Lấy trong `.env` (`GRAFANA_ADMIN_PASSWORD`) |
| **Prometheus** | `http://127.0.0.1:19090/targets` | Không yêu cầu | Truy cập trực tiếp qua SSH tunnel |

## 8. Kiểm tra và giám sát hệ thống

### Kiểm tra cấu hình Nginx và Security Headers

Chạy kiểm tra trên máy ảo:

```bash
docker compose exec nginx nginx -t
curl -I http://127.0.0.1:8080/
```

### Giám sát với Prometheus & Grafana

1. Truy cập `http://127.0.0.1:19090/targets` kiểm tra cả 4 target (`prometheus`, `nginx-exporter`, `mysql-exporter`, `cadvisor`) đều ở trạng thái **UP**.
2. Truy cập Grafana tại `http://127.0.0.1:13000`, mở thư mục **Dashboards** để xem biểu đồ về CPU/RAM container, trạng thái web server và database MySQL.

### Truy vấn Log tập trung với Loki & LogQL

Vào menu **Explore** trong Grafana, chọn data source **Loki**, sau đó thực hiện các truy vấn LogQL:

1. **Xem toàn bộ log các container:**
   ```logql
   {job="docker"}
   ```

2. **Lọc log các truy vấn Nginx trả về mã lỗi (status >= 400):**
   ```logql
   {job="nginx"} | json | status >= 400
   ```

3. **Thống kê số lượng request Nginx theo mã trạng thái HTTP trong 5 phút qua:**
   ```logql
   sum by (status) (count_over_time({job="nginx"}[5m]))
   ```

*(Bạn có thể thử truy cập một đường link không tồn tại trên web như `http://IP_VM:8080/notfound` để sinh ra log lỗi 404 phục vụ demo).*

## 9. Cấu hình bảo mật (Hardening)

Hệ thống tuân thủ chặt chẽ các biện pháp bảo mật thực hành:

1. **Cô lập mạng nội bộ (Network Isolation)**: Cơ sở dữ liệu MySQL nằm trong mạng `database` có thuộc tính `internal: true`, ngăn chặn mọi truy cập trực tiếp từ bên ngoài.
2. **Chạy container không có quyền root (Non-root Containers)**: Các dịch vụ Nginx, Prometheus, Grafana, Loki, Exporters đều được cấu hình chạy với user không phải root (UID riêng biệt) kèm `security_opt: no-new-privileges:true`.
3. **Giới hạn cổng quản trị**: phpMyAdmin, Prometheus, Grafana chỉ bind cổng vào loopback `127.0.0.1`, bắt buộc quản trị viên phải đi qua kênh mã hóa SSH Tunnel.
4. **Quản lý mật khẩu an toàn**: Toàn bộ mật khẩu DB và admin được phân tách rõ ràng qua file biến môi trường `.env`, không hardcode vào mã nguồn và file `.env` đã được chặn commit bởi `.gitignore`.
5. **Nginx Hardening**: Cấu hình thêm các security headers (`X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`), chặn truy cập tệp ẩn (dotfiles) và cấm thực thi mã PHP trong thư mục tải lên `uploads/`.

## 10. Quản lý vận hành dịch vụ

- **Dừng toàn bộ hệ thống tạm thời:**
  ```bash
  docker compose stop
  ```

- **Khởi động lại các dịch vụ đã dừng:**
  ```bash
  docker compose start
  ```

- **Gỡ bỏ container nhưng vẫn bảo toàn dữ liệu volume:**
  ```bash
  docker compose down
  ```

- **Khởi chạy lại sau khi down:**
  ```bash
  docker compose up -d
  ```

*(Lưu ý: Không sử dụng lệnh `docker compose down -v` trong quá trình sử dụng thông thường vì cờ `-v` sẽ xóa sạch các volume dữ liệu của database và website).*
