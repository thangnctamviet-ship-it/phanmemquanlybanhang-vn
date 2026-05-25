#!/usr/bin/env bash
# provision_tenant.sh <subdomain> <shop_name> <email> <admin_password>
# Tạo 1 tenant mới (DB + folder + admin user).
# MODE=local: dùng docker exec vào pmqlbh_db + symlink code.
# MODE=cpanel: gọi cPanel API (sẽ implement Phase 2).

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
LOG_FILE="$SCRIPT_DIR/provision.log"

# shellcheck disable=SC1091
source "$SCRIPT_DIR/lib/db_helpers.sh"
# shellcheck disable=SC1091
source "$SCRIPT_DIR/lib/cpanel_api.sh"

log() { echo "[$(date '+%F %T')] $*" | tee -a "$LOG_FILE" >&2; }

# Load .env
if [[ -f "$ROOT_DIR/.env" ]]; then
    # Read .env safely (avoid bash expansion on $-containing values like bcrypt hashes)
    while IFS='=' read -r key val; do
        [[ -z "${key:-}" || "$key" =~ ^# ]] && continue
        val="${val%\"}"; val="${val#\"}"
        export "$key"="$val"
    done < "$ROOT_DIR/.env"
fi
MODE="${MODE:-local}"

if [[ $# -lt 4 ]]; then
    echo '{"status":"error","message":"Usage: provision_tenant.sh <subdomain> <shop_name> <email> <password>"}' >&2
    exit 1
fi

SUBDOMAIN="$1"
SHOP_NAME="$2"
EMAIL="$3"
ADMIN_PASS="$4"

# Validate subdomain
if ! [[ "$SUBDOMAIN" =~ ^[a-z0-9][a-z0-9-]{1,28}[a-z0-9]$ ]]; then
    echo "{\"status\":\"error\",\"message\":\"Subdomain không hợp lệ: $SUBDOMAIN\"}"
    exit 2
fi

DB_NAME="tenant_${SUBDOMAIN}"
DB_USER="t_${SUBDOMAIN}"
DB_PASS="$(openssl rand -hex 12 2>/dev/null || echo "p$(date +%s)x")"

log "=== Provision: $SUBDOMAIN ($SHOP_NAME) MODE=$MODE ==="

# ---------- 1. Tạo DB + import schema ----------
if [[ "$MODE" == "local" ]]; then
    log "Tạo DB local: $DB_NAME"
    db_create "$DB_NAME"
    log "Import stock.sql vào $DB_NAME"
    db_import "$DB_NAME" "$ROOT_DIR/stock.sql"
    log "Xoá dữ liệu mẫu (chỉ giữ DDL + groups)"
    # giữ lại groups (Administrator) - chỉ xoá data tables. Re-import groups.
    docker exec -i "$DB_CONTAINER" mysql -u"$DB_ROOT_USER" -p"$DB_ROOT_PASS" "$DB_NAME" <<SQL
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE attribute_value;
TRUNCATE TABLE attributes;
TRUNCATE TABLE brands;
TRUNCATE TABLE categories;
TRUNCATE TABLE orders_item;
TRUNCATE TABLE orders;
TRUNCATE TABLE products;
TRUNCATE TABLE stores;
TRUNCATE TABLE users;
TRUNCATE TABLE user_group;
TRUNCATE TABLE company;
SET FOREIGN_KEY_CHECKS=1;
SQL

    # Seed: company info + admin user
    PASS_HASH="$(docker exec "${WEB_CONTAINER:-pmqlbh_web}" php -r "echo password_hash(\$argv[1], PASSWORD_BCRYPT);" "$ADMIN_PASS")"
    SHOP_NAME_ESC="${SHOP_NAME//\'/\\\'}"
    docker exec -i "$DB_CONTAINER" mysql -u"$DB_ROOT_USER" -p"$DB_ROOT_PASS" "$DB_NAME" <<SQL
INSERT INTO company (id, company_name, service_charge_value, vat_charge_value, address, phone, country, message, currency)
VALUES (1, '${SHOP_NAME_ESC}', '0', '10', '', '', 'Vietnam', '', 'VND');
INSERT INTO users (id, username, password, email, firstname, lastname, phone, gender)
VALUES (1, 'admin', '${PASS_HASH}', '${EMAIL}', 'Admin', '${SUBDOMAIN}', '', 1);
INSERT INTO user_group (id, user_id, group_id) VALUES (1, 1, 1);
SQL
    log "Đã seed company + admin user"

elif [[ "$MODE" == "cpanel" ]]; then
    log "Tạo DB qua cPanel API: $DB_NAME"
    cpanel_create_db "$DB_NAME"
    cpanel_create_db_user "$DB_USER" "$DB_PASS"
    cpanel_grant_db "$DB_USER" "$DB_NAME"
    log "TODO: import schema qua cPanel (Phase 2)"
fi

# ---------- 2. Tạo folder tenant + symlink code ----------
TENANT_DIR="$ROOT_DIR/tenants/$SUBDOMAIN"
mkdir -p "$TENANT_DIR"

if [[ "$MODE" == "local" ]]; then
    log "Tạo symlink code vào $TENANT_DIR"
    # symlink các folder/file cốt lõi, NHƯNG copy folder application/config
    for item in application assets system index.php .htaccess composer.json composer.lock tenant-shared; do
        if [[ -e "$ROOT_DIR/$item" ]]; then
            ln -sfn "$ROOT_DIR/$item" "$TENANT_DIR/$item" 2>/dev/null || true
        fi
    done
    # Xoá symlink application & copy lại với config riêng
    rm -f "$TENANT_DIR/application"
    mkdir -p "$TENANT_DIR/application"
    for sub in "$ROOT_DIR"/application/*; do
        name="$(basename "$sub")"
        if [[ "$name" == "config" ]]; then
            cp -R "$sub" "$TENANT_DIR/application/config"
        else
            ln -sfn "$sub" "$TENANT_DIR/application/$name"
        fi
    done

    # Sửa database.php
    CFG="$TENANT_DIR/application/config/database.php"
    sed -i.bak "s/'database' => 'stock'/'database' => '${DB_NAME}'/" "$CFG"
    rm -f "$CFG.bak"
    log "Đã cập nhật database.php: database=${DB_NAME}"

elif [[ "$MODE" == "cpanel" ]]; then
    log "TODO: tạo subdomain qua cPanel UAPI"
    cpanel_create_subdomain "$SUBDOMAIN" "${CPANEL_DOMAIN:-quanlybanhang.shop}" "tenants/${SUBDOMAIN}"
fi

# ---------- 3. Output JSON ----------
URL_LOCAL="http://${SUBDOMAIN}.localhost:8080"
URL_PROD="https://${SUBDOMAIN}.${CPANEL_DOMAIN:-quanlybanhang.shop}"
URL="$URL_LOCAL"
[[ "$MODE" == "cpanel" ]] && URL="$URL_PROD"

cat <<JSON
{"status":"ok","subdomain":"${SUBDOMAIN}","db_name":"${DB_NAME}","db_user":"${DB_USER}","db_pass":"${DB_PASS}","url":"${URL}","admin_email":"${EMAIL}","admin_password_temp":"${ADMIN_PASS}"}
JSON

log "=== DONE: $SUBDOMAIN ==="
