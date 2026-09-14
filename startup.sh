#!/bin/bash

sed -i 's#root /home/site/wwwroot;#root /home/site/wwwroot/public;#g' /etc/nginx/sites-available/default

# Rewrite the "location / { ... }" block from scratch on every boot instead of
# a grep-gated insert. The old grep check could match "try_files" anywhere in
# the file (e.g. a leftover/partial line from a previous boot) and then skip
# adding it to this block, leaving that specific instance unable to route
# non-root URLs (e.g. /supplier/dashboard) to index.php -> raw nginx 404.
# This version is idempotent: safe to run on every restart/new instance and
# always produces the same, correct config.
sed -i '/location \/ {/,/}/{/try_files/d}' /etc/nginx/sites-available/default
sed -i '/location \/ {/a\        try_files $uri $uri/ /index.php?$query_string;' /etc/nginx/sites-available/default

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
