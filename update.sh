#!/bin/sh
git fetch all;
git reset --hard origin/master;
git pull;
mv ./laravel/* /var/www/laravel/
