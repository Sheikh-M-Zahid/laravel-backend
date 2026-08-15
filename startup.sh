#!/bin/bash

sed -i 's#root /home/site/wwwroot;#root /home/site/wwwroot/public;#g' /etc/nginx/sites-available/default

if ! grep -q "try_files" /etc/nginx/sites-available/default; then
    sed -i '/location \/ {/a\        try_files $uri $uri/ /index.php?$query_string;' /etc/nginx/sites-available/default
fi

mkdir -p /home/site/wwwroot/bootstrap/cache
mkdir -p /home/site/wwwroot/storage/framework/cache
mkdir -p /home/site/wwwroot/storage/framework/sessions
mkdir -p /home/site/wwwroot/storage/framework/views
mkdir -p /home/site/wwwroot/storage/app/public
chmod -R 775 /home/site/wwwroot/storage
chmod -R 775 /home/site/wwwroot/bootstrap/cache

nginx -t && (pkill nginx; sleep 1; nginx)
