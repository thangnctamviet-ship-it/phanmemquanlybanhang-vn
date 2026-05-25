# 🧪 Hướng dẫn test phần mềm

Có 2 cách để chạy thử phần mềm này mà không cần cài PHP/MySQL trực tiếp lên máy.

---

## 🐳 Cách 1: Docker (chạy offline trên máy bạn)

### Bước 1 — Cài Docker
- **Mac (Apple Silicon):** tải [OrbStack](https://orbstack.dev) (nhẹ hơn Docker Desktop) hoặc [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- **Windows:** Docker Desktop
- **Linux:** `curl -fsSL https://get.docker.com | sh`

### Bước 2 — Clone repo
```bash
git clone https://github.com/thangnctamviet-ship-it/phanmemquanlybanhang-vn.git
cd phanmemquanlybanhang-vn
```

### Bước 3 — Khởi động
```bash
docker compose up -d
```

Đợi ~1 phút lần đầu (tải image PHP + MySQL).

### Bước 4 — Truy cập
| Dịch vụ | URL | Tài khoản |
|---|---|---|
| **Ứng dụng** | http://localhost:8080 | (xem trong bảng `users` của DB) |
| **phpMyAdmin** | http://localhost:8081 | root / root |
| **MySQL** (host) | localhost:3307 | root / root |

### Bước 5 — Dừng
```bash
docker compose down          # dừng
docker compose down -v       # dừng + xoá luôn database
```

---

## ☁️ Cách 2: GitHub Codespaces (chạy online, không cần cài gì)

### Bước 1
Vào repo trên GitHub: https://github.com/thangnctamviet-ship-it/phanmemquanlybanhang-vn

### Bước 2
Bấm nút **`Code`** màu xanh → tab **`Codespaces`** → **`Create codespace on main`**

### Bước 3
Đợi ~2 phút. GitHub sẽ tự dựng máy ảo + Docker + VSCode trong trình duyệt.

### Bước 4
Khi xong, một popup hiện ra hỏi mở **Port 8080** → bấm **Open in Browser** → ứng dụng chạy.

### Lưu ý
- Miễn phí **60 giờ/tháng** với tài khoản GitHub thường.
- Codespace tự ngủ sau 30 phút không dùng → không tốn giờ.
- Có thể xoá codespace bất cứ lúc nào (Settings → Codespaces).

---

## ⚙️ Cấu hình ứng dụng

File `application/config/config.php` mặc định:
```php
$config['base_url'] = 'http://localhost/InventorySystem/';
```

**Nếu chạy bằng Docker → đổi thành:**
```php
$config['base_url'] = 'http://localhost:8080/';
```

**Nếu chạy bằng Codespaces → đổi thành URL forwarded port** (GitHub sẽ cho bạn 1 URL dạng `https://xxx-8080.app.github.dev/`).

File `application/config/database.php`:
```php
'hostname' => 'db',          // tên service trong docker-compose
'username' => 'root',
'password' => 'root',
'database' => 'stock',
```

---

## 🆘 Sự cố thường gặp

| Lỗi | Cách xử lý |
|---|---|
| Trang trắng / lỗi 500 | Check log: `docker compose logs web` |
| DB connection refused | Đợi thêm 30s rồi reload (MySQL khởi động chậm lần đầu) |
| Không có bảng nào trong DB | Chạy lại: `docker compose down -v && docker compose up -d` |
| Quên mật khẩu admin | Vào phpMyAdmin → bảng `users` → reset password |
