#!/bin/bash
sed -i 's#root /home/site/wwwroot;#root /home/site/wwwroot/public;#g' /etc/nginx/sites-available/default
service nginx reload
