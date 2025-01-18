#!/bin/sh
cd /app

composer install --ignore-platform-reqs

php init --env=Development --overwrite=No

#chmod 755 -R frontend/web
#chmod 755 -R backend/web

#php yii migrate --interactive=0

#php-fpm
apache2-foreground
