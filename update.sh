#!/bin/sh
git fetch all;
git reset --hard origin/master;
git pull;
rsync -ravv ./laravel/* /var/www/laravel/
