#!/usr/bin/env bash
# Wrapper cho cPanel UAPI - chỉ là placeholder cho Phase 2
# Sẽ implement khi có CPANEL_HOST + CPANEL_USER + CPANEL_TOKEN

cpanel_uapi() {
    local module="$1" func="$2"; shift 2
    if [[ -z "$CPANEL_HOST" || -z "$CPANEL_USER" || -z "$CPANEL_TOKEN" ]]; then
        echo "[cpanel_api] BỎ QUA - thiếu cấu hình cPanel ($module::$func)" >&2
        return 0
    fi
    local qs=""
    for kv in "$@"; do qs+="&${kv}"; done
    curl -sk -H "Authorization: cpanel ${CPANEL_USER}:${CPANEL_TOKEN}" \
        "https://${CPANEL_HOST}:2083/execute/${module}/${func}?${qs#&}"
}

cpanel_create_subdomain() {
    local sub="$1" domain="$2" dir="$3"
    cpanel_uapi SubDomain addsubdomain \
        "domain=${sub}" "rootdomain=${domain}" "dir=${dir}"
}

cpanel_create_db() {
    local db="$1"
    cpanel_uapi Mysql create_database "name=${db}"
}

cpanel_create_db_user() {
    local user="$1" pass="$2"
    cpanel_uapi Mysql create_user "name=${user}" "password=${pass}"
}

cpanel_grant_db() {
    local user="$1" db="$2"
    cpanel_uapi Mysql set_privileges_on_database "user=${user}" "database=${db}" "privileges=ALL PRIVILEGES"
}
