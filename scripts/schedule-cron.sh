#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd -- "${SCRIPT_DIR}/.." && pwd)"
ARTISAN_FILE="${PROJECT_DIR}/artisan"
CRON_TAG="# sekolah-ict-laravel-schedule"
LOG_FILE="${PROJECT_DIR}/storage/logs/schedule-cron.log"

usage() {
    cat <<'EOF'
Penggunaan: scripts/schedule-cron.sh {install|remove|status}

  install  Pasang cron Laravel scheduler setiap menit.
  remove   Hapus cron Laravel scheduler milik project ini.
  status   Tampilkan status cron Laravel scheduler project ini.
EOF
}

current_crontab() {
    crontab -l 2>/dev/null || true
}

ensure_requirements() {
    if ! command -v crontab >/dev/null 2>&1; then
        echo 'Perintah crontab tidak ditemukan.' >&2
        exit 1
    fi

    if [[ ! -f "${ARTISAN_FILE}" ]]; then
        echo "File artisan tidak ditemukan: ${ARTISAN_FILE}" >&2
        exit 1
    fi
}

install_cron() {
    ensure_requirements

    if current_crontab | grep -Fq "${CRON_TAG}"; then
        echo 'Cron Laravel scheduler sudah terpasang.'
        return
    fi

    local php_binary
    php_binary="$(command -v php || true)"

    if [[ -z "${php_binary}" ]]; then
        echo 'PHP tidak ditemukan.' >&2
        exit 1
    fi

    mkdir -p "$(dirname -- "${LOG_FILE}")"

    {
        current_crontab
        printf '* * * * * cd "%s" && "%s" artisan schedule:run >> "%s" 2>&1 %s\n' \
            "${PROJECT_DIR}" \
            "${php_binary}" \
            "${LOG_FILE}" \
            "${CRON_TAG}"
    } | crontab -

    echo 'Cron Laravel scheduler berhasil dipasang.'
}

remove_cron() {
    ensure_requirements

    if ! current_crontab | grep -Fq "${CRON_TAG}"; then
        echo 'Cron Laravel scheduler tidak ditemukan.'
        return
    fi

    local temporary_crontab
    temporary_crontab="$(mktemp)"
    trap 'rm -f "${temporary_crontab}"' EXIT

    current_crontab | grep -Fv "${CRON_TAG}" > "${temporary_crontab}" || true
    crontab "${temporary_crontab}"

    echo 'Cron Laravel scheduler berhasil dihapus.'
}

status_cron() {
    ensure_requirements

    if current_crontab | grep -F "${CRON_TAG}"; then
        return
    fi

    echo 'Cron Laravel scheduler belum terpasang.'
    return 1
}

case "${1:-}" in
    install)
        install_cron
        ;;
    remove)
        remove_cron
        ;;
    status)
        status_cron
        ;;
    *)
        usage
        exit 1
        ;;
esac
