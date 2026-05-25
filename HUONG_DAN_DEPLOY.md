# Hướng dẫn deploy HawkHost/cPanel

## 1. DNS wildcard

Trong DNS zone của `quanlybanhang.shop`, tạo bản ghi:

```text
*.quanlybanhang.shop  A  <IP hosting HawkHost>
quanlybanhang.shop    A  <IP hosting HawkHost>
```

Đợi DNS propagate rồi kiểm tra bằng `dig demo.quanlybanhang.shop`.

## 2. Upload code

Tạo `.env` trên server từ mẫu local, không commit secret. Chạy deploy từ máy local:

```bash
bash scripts/deploy_code.sh
```

Script dùng `rsync` nếu có `CPANEL_SSH_KEY`, fallback `lftp` nếu có `CPANEL_PASSWORD`. Các thư mục `.git`, `.env`, `tenants/`, cache/logs và schema mẫu được exclude.

## 3. Import master DB

Vào cPanel → phpMyAdmin:

1. Tạo database master, ví dụ `iqosvnsh_master_quanlybanhang`.
2. Import `master_schema.sql`.
3. Cập nhật `.env`:

```ini
MODE=cpanel
MASTER_DB_HOST=localhost
MASTER_DB_NAME=iqosvnsh_master_quanlybanhang
MASTER_DB_USER=<user>
MASTER_DB_PASS=<password>
CPANEL_HOST=hkg100.arandomserver.com:2083
CPANEL_USER=iqosvnsh
CPANEL_TOKEN=<token>
CPANEL_DOMAIN=quanlybanhang.shop
BASE_DOMAIN=quanlybanhang.shop
```

## 4. Test cPanel token

Trên server hoặc máy local có `.env`:

```bash
source scripts/lib/cpanel_api.sh
set -a; . ./.env; set +a
cpanel_test_connectivity
```

Hoặc test curl trực tiếp:

```bash
curl -sk -H "Authorization: cpanel ${CPANEL_USER}:${CPANEL_TOKEN}" \
  "https://${CPANEL_HOST}/execute/Version/version"
```

JSON trả về cần có `"status":1`.

## 5. Landing và đăng ký test

Trỏ web root chính vào thư mục repo, mở:

```text
https://quanlybanhang.shop/landing/
```

Đăng ký thử một tenant. Nếu không có `CPANEL_REMOTE_DB_HOST`, script sẽ tạo DB/user/subdomain và ghi log yêu cầu import `stock.sql` thủ công qua phpMyAdmin vào DB tenant.

## 6. Cron hết hạn

Trong cPanel Cron Jobs, thêm:

```cron
0 9 * * * /usr/bin/php /path/to/cron_check_expired.php
```

Đường dẫn thực tế thường là:

```cron
0 9 * * * /usr/bin/php /home/<cpanel_user>/public_html/scripts/cron_check_expired.php
```

Cron sẽ đánh dấu tenant hết hạn và gửi email nhắc trước 3 ngày, 1 ngày, và ngày hết hạn.
