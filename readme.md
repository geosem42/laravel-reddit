
## Installation on an Ubuntu 16.04 VPS / virtual box


1. Run "git clone https://github.com/Project-Oblio/laravel-irt.git; cd laravel-irt; ./install.sh". There are some manual configurations needed:

2. Inside the first file that opens, keep "server_name 127.0.0.1" for localhost/docker. Change to  "server_name {{public-ip-address}}" or "server_name {{domain name}}" if running on a VPS.

3. Make the mysql password this: anyPassword. Keep entering it every time it asks. When asked, turn off all test accounts and test databases. When asked, install phpmyadmin as an apache server. 

## Test posts
This will set up tests posts for your db

crontab -e

5 * * * * php /var/www/laravel schedule:run >> /dev/null 2>&1
