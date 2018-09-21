#!/bin/sh
if [ $# -eq 0 ] ; then
    echo 'Please enter a branch name'
    exit 1
fi

branch=$1;
git fetch;
#git reset --hard origin/master;
git pull origin $branch;
rsync -ravv ./laravel/* /var/www/laravel/
cp ./laravel/.* /var/www/laravel/
chown -R :www-data /var/www/laravel; 
chmod -R 775 /var/www/laravel/storage; 
chmod -R 775 /var/www/laravel/bootstrap/cache; 

if [ $# -eq 2 ] || [ $# -eq 3 ] ; then 
	cd /var/www/; 
	cd laravel;
	rm -rf vendor;
	composer clearcache;
	composer dump-autoload;
	php artisan clear-compiled; 
	rm -rf bootstrap/cache/packages.php;
	rm -rf bootstrap/cache/services.php;
	composer install --no-scripts;
	composer update; 
	php artisan key:generate;
	chown -R :www-data /var/www/laravel; 
	chmod -R 775 /var/www/laravel/storage; 
	chmod -R 775 /var/www/laravel/bootstrap/cache; 

	if [ $# -eq 3 ] ; then
    		echo 'Updating mysql'
   		mysql -uroot -panyPassword -e "drop database irt;" 
    		#mysql -uroot -panyPassword -e "create database irt;"
	       mysql -uroot -panyPassword irt < /root/laravel-irt/backupDatabases/databaseBackup1.sql;

	fi
fi
cd /var/www/laravel/;
php artisan migrate; 
php artisan serve --host "poster.projectoblio.com"
