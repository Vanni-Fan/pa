#!/bin/bash
# 使用 Root 启动
echo 使用 Root 启动 Nginx
sed -i "s/nginx;/root;/g" /etc/nginx/nginx.conf
echo 启动命令： nginx-debug -g "daemon off;"
nginx-debug -g "daemon off;"