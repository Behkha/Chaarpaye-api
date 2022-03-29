#!/bin/sh
# activate maintenance mode
#php artisan down
# update source code
sudo -u www-data git pull 1>>test.txt 2>&1
# update PHP dependencies
sudo -u www-data composer install --no-interaction --no-dev --prefer-dist 1>>test.txt 2>&1
# --no-interaction Do not ask any interactive question
# --no-dev  Disables installation of require-dev packages.
# --prefer-dist  Forces installation from package dist even for dev versions.
# update database
sudo -u www-data php artisan migrate --force 1>>test.txt 2>&1
sudo -u www-data php artisan queue:restart 1>>test.txt 2>&1
# --force  Required to run when in production.
# stop maintenance mode
#php artisan up
