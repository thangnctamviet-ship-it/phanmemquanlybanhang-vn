#!/usr/bin/env bash
# deprovision_tenant.sh <subdomain> — xoá DB + folder tenant (local mode)
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
source "$SCRIPT_DIR/lib/db_helpers.sh"

[[ -f "$ROOT_DIR/.env" ]] && { set -a; . "$ROOT_DIR/.env"; set +a; }
MODE="${MODE:-local}"

SUB="${1:?Usage: deprovision_tenant.sh <subdomain>}"
DB="tenant_${SUB}"

if [[ "$MODE" == "local" ]]; then
    db_drop "$DB"
    rm -rf "$ROOT_DIR/tenants/$SUB"
    echo "{\"status\":\"ok\",\"removed\":\"${SUB}\"}"
else
    echo "{\"status\":\"todo\",\"message\":\"cPanel deprovision chưa implement\"}"
fi
