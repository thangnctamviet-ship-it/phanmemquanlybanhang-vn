#!/usr/bin/env bash
# Wrapper cho cPanel UAPI.

cpanel_api_log() { echo "[cpanel_api] $*" >&2; }

cpanel_require_env() {
    local missing=()
    [[ -n "${CPANEL_HOST:-}" ]] || missing+=("CPANEL_HOST")
    [[ -n "${CPANEL_USER:-}" ]] || missing+=("CPANEL_USER")
    [[ -n "${CPANEL_TOKEN:-}" ]] || missing+=("CPANEL_TOKEN")
    if (( ${#missing[@]} > 0 )); then
        cpanel_api_log "Thiếu cấu hình: ${missing[*]}"
        return 2
    fi
}

cpanel_encode() {
    python3 -c 'import sys, urllib.parse; print(urllib.parse.quote_plus(sys.argv[1]))' "$1" 2>/dev/null \
        || python -c 'import sys, urllib; print(urllib.quote_plus(sys.argv[1]))' "$1"
}

cpanel_json_status_ok() {
    if command -v jq >/dev/null 2>&1; then
        jq -e '.status == 1' >/dev/null
    else
        python3 -c 'import json,sys; sys.exit(0 if json.load(sys.stdin).get("status")==1 else 1)' 2>/dev/null \
            || python -c 'import json,sys; sys.exit(0 if json.load(sys.stdin).get("status")==1 else 1)'
    fi
}

cpanel_uapi() {
    local module="$1" func="$2"; shift 2
    cpanel_require_env || return $?

    local host="${CPANEL_HOST#https://}"
    host="${host#http://}"
    host="${host%/}"
    host="${host%:2083}"

    local qs="" key val
    for kv in "$@"; do
        key="${kv%%=*}"
        val="${kv#*=}"
        qs+="&$(cpanel_encode "$key")=$(cpanel_encode "$val")"
    done

    local url="https://${host}:2083/execute/${module}/${func}?${qs#&}"
    local auth="Authorization: cpanel ${CPANEL_USER}:${CPANEL_TOKEN}"

    if [[ "${DRY_RUN:-0}" == "1" ]]; then
        printf 'curl -sk -H %q %q\n' "$auth" "$url"
        return 0
    fi

    local response
    response="$(curl -sk -H "$auth" "$url")" || {
        cpanel_api_log "curl fail: ${module}/${func}"
        return 1
    }

    if printf '%s' "$response" | cpanel_json_status_ok; then
        printf '%s\n' "$response"
        return 0
    fi

    cpanel_api_log "UAPI fail ${module}/${func}: $response"
    return 1
}

cpanel_create_database() {
    local db="$1"
    cpanel_uapi Mysql create_database "name=${db}"
}

cpanel_create_db_user() {
    local user="$1" pass="$2"
    cpanel_uapi Mysql create_user "name=${user}" "password=${pass}"
}

cpanel_grant_db_user() {
    local db="$1" user="$2"
    cpanel_uapi Mysql set_privileges_on_database "database=${db}" "user=${user}" "privileges=ALL PRIVILEGES"
}

cpanel_create_subdomain() {
    local sub="$1" domain="$2" dir="$3"
    cpanel_uapi SubDomain addsubdomain "domain=${sub}" "rootdomain=${domain}" "dir=${dir}"
}

cpanel_create_ftp_account() {
    local user="$1" pass="$2" dir="$3" quota="${4:-0}"
    cpanel_uapi Ftp add_ftp "user=${user}" "pass=${pass}" "homedir=${dir}" "quota=${quota}"
}

cpanel_test_connectivity() {
    cpanel_uapi Version version
}

# Backward-compatible names from Phase 1.
cpanel_create_db() { cpanel_create_database "$@"; }
cpanel_grant_db() {
    local user="$1" db="$2"
    cpanel_grant_db_user "$db" "$user"
}
