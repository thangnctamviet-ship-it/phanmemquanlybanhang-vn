#!/usr/bin/env bash
# Deploy code lên cPanel bằng rsync qua SSH/SFTP hoặc lftp qua FTP.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

if [[ -f "$ROOT_DIR/.env" ]]; then
    while IFS='=' read -r key val; do
        [[ -z "${key:-}" || "$key" =~ ^# ]] && continue
        val="${val%\"}"; val="${val#\"}"
        export "$key"="$val"
    done < "$ROOT_DIR/.env"
fi

REMOTE_PATH="${CPANEL_REMOTE_PATH:-public_html}"
HOST="${CPANEL_HOST:-}"
HOST="${HOST#https://}"; HOST="${HOST#http://}"; HOST="${HOST%/}"; HOST="${HOST%:2083}"

echo "Deploy variables:"
echo "  CPANEL_HOST=${HOST:-<missing>}"
echo "  CPANEL_USER=${CPANEL_USER:-<missing>}"
echo "  CPANEL_REMOTE_PATH=${REMOTE_PATH}"
echo "  CPANEL_SSH_KEY=${CPANEL_SSH_KEY:-<empty>}"
echo "  CPANEL_PASSWORD=${CPANEL_PASSWORD:+<set>}"

if [[ -z "${HOST:-}" || -z "${CPANEL_USER:-}" ]]; then
    echo "Thiếu CPANEL_HOST hoặc CPANEL_USER" >&2
    exit 2
fi

EXCLUDES=(
    "--exclude=.git"
    "--exclude=.env"
    "--exclude=.env.secrets"
    "--exclude=tenants/"
    "--exclude=scripts/provision.log"
    "--exclude=master_schema.sql"
    "--exclude=application/cache/*"
    "--exclude=application/logs/*"
)

if [[ -n "${CPANEL_SSH_KEY:-}" && -f "${CPANEL_SSH_KEY}" ]]; then
    echo "Deploy bằng rsync qua SSH key..."
    rsync -az --delete "${EXCLUDES[@]}" \
        -e "ssh -i ${CPANEL_SSH_KEY} -p ${CPANEL_SSH_PORT:-22} -o StrictHostKeyChecking=accept-new" \
        "$ROOT_DIR/" "${CPANEL_USER}@${HOST}:${REMOTE_PATH}/"
    exit 0
fi

if [[ -z "${CPANEL_PASSWORD:-}" ]]; then
    echo "Không có CPANEL_SSH_KEY hợp lệ; cần CPANEL_PASSWORD để fallback FTP." >&2
    exit 2
fi

if ! command -v lftp >/dev/null 2>&1; then
    echo "Thiếu lftp. Cài lftp hoặc cấu hình CPANEL_SSH_KEY để dùng rsync." >&2
    exit 2
fi

echo "Deploy bằng lftp FTP..."
lftp -u "${CPANEL_USER},${CPANEL_PASSWORD}" "${HOST}" <<LFTP
set ftp:ssl-allow yes
set ssl:verify-certificate no
mirror -R --delete \
  --exclude-glob .git \
  --exclude-glob .env \
  --exclude-glob .env.secrets \
  --exclude-glob tenants/ \
  --exclude-glob scripts/provision.log \
  --exclude-glob master_schema.sql \
  --exclude-glob application/cache/* \
  --exclude-glob application/logs/* \
  "$ROOT_DIR" "$REMOTE_PATH"
bye
LFTP
