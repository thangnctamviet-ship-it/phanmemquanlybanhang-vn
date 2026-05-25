#!/usr/bin/env bash
# Helpers cho thao tác DB local (qua docker exec)

DB_CONTAINER="${DB_CONTAINER:-pmqlbh_db}"
DB_ROOT_USER="${DB_ROOT_USER:-root}"
DB_ROOT_PASS="${DB_ROOT_PASS:-root}"

db_exec() {
    docker exec -i "$DB_CONTAINER" mysql -u"$DB_ROOT_USER" -p"$DB_ROOT_PASS" "$@"
}

db_create() {
    local db="$1"
    db_exec -e "CREATE DATABASE IF NOT EXISTS \`$db\` DEFAULT CHARSET utf8mb4;"
}

db_drop() {
    local db="$1"
    db_exec -e "DROP DATABASE IF EXISTS \`$db\`;"
}

db_import() {
    local db="$1" file="$2"
    db_exec "$db" < "$file"
}

# Xoá dữ liệu mẫu giữ lại DDL + bảng nền tảng (groups, company)
db_wipe_sample_data() {
    local db="$1"
    db_exec "$db" <<'SQL'
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
SET FOREIGN_KEY_CHECKS=1;
SQL
}
