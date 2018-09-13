#!/bin/sh
git fetch all;
git reset --hard origin/master;
git pull;
rsync -ravv ./laravel/* /var/www/laravel/
cd /var/www/; 
chown -R :www-data /var/www/laravel; 
chmod -R 775 /var/www/laravel/storage; 
chmod -R 775 /var/www/laravel/bootstrap/cache; 
