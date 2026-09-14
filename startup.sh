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

# Profile photos (and any other public upload) live in storage/app/public and
# are served through the public/storage symlink -- `php artisan storage:link`
# normally creates this, but Azure's filesystem doesn't reliably persist it
# across restarts/deploys, so re-create it here on every boot if missing.
if [ ! -e /home/site/wwwroot/public/storage ]; then
    ln -s /home/site/wwwroot/storage/app/public /home/site/wwwroot/public/storage
fi

nginx -t && (pkill nginx; sleep 1; nginx)
