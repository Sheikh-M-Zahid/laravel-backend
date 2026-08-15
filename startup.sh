#!/bin/bash

# Fix nginx root to point to Laravel's public folder
sed -i 's#root /home/site/wwwroot;#root /home/site/wwwroot/public;#g' /etc/nginx/sites-available/default

# Add try_files so Laravel routes (not just real files) work
if ! grep -q "try_files" /etc/nginx/sites-available/default; then
    sed -i '/location \/ {/a\        try_files $uri $uri/ /index.php?$query_string;' /etc/nginx/sites-available/default
fi

# Ensure required Laravel folders exist
mkdir -p /home/site/wwwroot/bootstrap/cache
mkdir -p /home/site/wwwroot/storage/framework/cache
mkdir -p /home/site/wwwroot/storage/framework/sessions
mkdir -p /home/site/wwwroot/storage/framework/views
mkdir -p /home/site/wwwroot/storage/app/public
chmod -R 775 /home/site/wwwroot/storage
chmod -R 775 /home/site/wwwroot/bootstrap/cache

# Restart nginx cleanly (reload signal proved unreliable)
nginx -t && (pkill nginx; sleep 1; nginx)
