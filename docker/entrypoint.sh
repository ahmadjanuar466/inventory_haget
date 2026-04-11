#!/bin/sh

set -eu

APP_DIR="/var/www/html"
IMAGE_APP_DIR="/opt/app"

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

    chown -R www-data:www-data "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
    chmod -R ug+rwx "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
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

copy_tree_if_missing "${IMAGE_APP_DIR}/vendor" "${APP_DIR}/vendor" "autoload.php"
copy_tree_if_missing "${IMAGE_APP_DIR}/public/build" "${APP_DIR}/public/build" "manifest.json"

ensure_runtime_directories
clear_bootstrap_cache
wait_for_database

php artisan package:discover --ansi
php artisan migrate --force --ansi
ensure_storage_link

exec "$@"
