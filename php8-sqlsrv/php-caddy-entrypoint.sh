#!/bin/bash
# set -o pipefail
set -e

# กำหนด directory สำหรับ PHP และ PHP-FPM config
FPM_CONFIG_DIR=/usr/local/etc/php-fpm.d
PHP_INI_DIR=/usr/local/etc/php

# Function สำหรับอ่าน environment variables และสร้าง config file
read_env_config() {
    prefix=$1
    output=$2

    # ลบไฟล์ output ก่อน หากมีอยู่แล้ว เพื่อป้องกันการเพิ่มข้อมูลซ้ำซ้อน
    if [ -f "$output" ]; then
        rm -f "$output"
    fi

    # อ่าน environment variables ที่ขึ้นต้นด้วย prefix และสร้าง config file
    env | grep -E "^$prefix\..+=.+" | while IFS='=' read -r config_name config_value; do
        echo "${config_name#*.} = $config_value" >> "$output"
    done
}

# อ่าน environment variables และสร้างไฟล์ config สำหรับ PHP และ PHP-FPM
read_env_config "PHP" "$PHP_INI_DIR/conf.d/zz-docker-env.ini"
read_env_config "PHP_FPM" "$FPM_CONFIG_DIR/zz-docker.conf"

# เริ่มต้น php-fpm ใน background
php-fpm -D

# Execute command ที่ระบุใน container (เช่น caddy run)
exec "$@"