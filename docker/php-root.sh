#!/bin/bash
# 使用 Root 启动
echo 使用 Root 启动 PHP 
sed -i "s/www-data/root/g" /usr/local/etc/php-fpm.d/www.conf
echo 启动命令： docker-php-entrypoint -R "$@"
docker-php-entrypoint -R "$@"