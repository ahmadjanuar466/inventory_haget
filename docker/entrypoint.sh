#!/bin/sh

set -eu

APP_DIR="/var/www/html"
IMAGE_APP_DIR="/opt/app"
APP_UID="${WWWUSER:-1000}"
APP_GID="${WWWGROUP:-1000}"
ALLOW_WORLD_WRITABLE_STORAGE="${ALLOW_WORLD_WRITABLE_STORAGE:-false}"
FIX_APP_SOURCE_PERMISSIONS="${FIX_APP_SOURCE_PERMISSIONS:-false}"

validate_app_id() {
    value="$1"
    name="$2"

    case "$value" in
        ''|*[!0-9]*)
            echo "${name} must be numeric, got '${value}'." >&2
            exit 1
            ;;
    esac
}

configure_app_user() {
    validate_app_id "$APP_UID" "WWWUSER"
    validate_app_id "$APP_GID" "WWWGROUP"

    if [ "$(id -g www-data)" != "$APP_GID" ]; then
        groupmod -o -g "$APP_GID" www-data
    fi

    if [ "$(id -u www-data)" != "$APP_UID" ]; then
        usermod -o -u "$APP_UID" www-data
    fi
}

copy_tree_if_missing() {
    source_dir="$1"
    target_dir="$2"
    sentinel="$3"

    if [ -e "${target_dir}/${sentinel}" ]; then
        return 0
    fi

    mkdir -p "$target_dir"
    cp -a "${source_dir}/." "$target_dir/"
}

sync_tree() {
    source_dir="$1"
    target_dir="$2"

    mkdir -p "$target_dir"
    cp -a "${source_dir}/." "$target_dir/"
}

ensure_runtime_directories() {
    mkdir -p \
        "${APP_DIR}/bootstrap/cache" \
        "${APP_DIR}/public/build" \
        "${APP_DIR}/storage/app/public" \
        "${APP_DIR}/storage/framework/cache/data" \
        "${APP_DIR}/storage/framework/sessions" \
        "${APP_DIR}/storage/framework/views" \
        "${APP_DIR}/storage/logs"

    touch "${APP_DIR}/storage/logs/laravel.log"

    chown -R "${APP_UID}:${APP_GID}" \
        "${APP_DIR}/storage" \
        "${APP_DIR}/bootstrap/cache" \
        /run/php \
        /var/lib/nginx \
        /var/log/nginx

    if [ "$ALLOW_WORLD_WRITABLE_STORAGE" = "true" ]; then
        chmod -R a+rwX "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
    else
        chmod -R ug+rwx "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
    fi
}

ensure_source_permissions() {
    if [ "$FIX_APP_SOURCE_PERMISSIONS" != "true" ]; then
        return 0
    fi

    for path in app config database public resources routes tests artisan composer.json composer.lock package.json package-lock.json phpunit.xml vite.config.js; do
        if [ -e "${APP_DIR}/${path}" ]; then
            chown -R "${APP_UID}:${APP_GID}" "${APP_DIR}/${path}"
        fi
    done
}

clear_bootstrap_cache() {
    rm -f "${APP_DIR}/bootstrap/cache/"*.php
}

wait_for_database() {
    if [ "${DB_CONNECTION:-}" != "mysql" ]; then
        return 0
    fi

    echo "Waiting for MySQL at ${DB_HOST:-app_mysql}:${DB_PORT:-3306}..."

    for _ in $(seq 1 60); do
        if mysqladmin ping \
            -h"${DB_HOST:-app_mysql}" \
            -P"${DB_PORT:-3306}" \
            -u"${DB_USERNAME:-app_user}" \
            -p"${DB_PASSWORD:-app_pass}" \
            --silent >/dev/null 2>&1; then
            return 0
        fi

        sleep 2
    done

    echo "MySQL did not become ready in time." >&2
    return 1
}

ensure_storage_link() {
    if [ -L "${APP_DIR}/public/storage" ] || [ -e "${APP_DIR}/public/storage" ]; then
        return 0
    fi

    php artisan storage:link --ansi
}

cd "$APP_DIR"

configure_app_user

if [ "$ALLOW_WORLD_WRITABLE_STORAGE" = "true" ]; then
    umask 0000
else
    umask 0002
fi

copy_tree_if_missing "${IMAGE_APP_DIR}/vendor" "${APP_DIR}/vendor" "autoload.php"
sync_tree "${IMAGE_APP_DIR}/public/build" "${APP_DIR}/public/build"

ensure_source_permissions
ensure_runtime_directories
clear_bootstrap_cache
wait_for_database

php artisan package:discover --ansi
php artisan migrate --force --ansi
ensure_storage_link
ensure_runtime_directories

exec "$@"
