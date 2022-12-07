<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


Copy environment constants: <br>
cp .env.example .env

install composer dependencies (use composer v2.*): <br>

composer install

Build setupped config for DB, php and scheduler (There is only php8.1 build): <br>
vendor/bin/sail build --no-cache

Start a container <br>
vendor/bin/sail up -d 

Migrate tables with: <br>
vendor/bin/sail migrate

There is possibility that You can catch logger permision error, so You need to: <br>

chown -R www-data:www-data storage/ <br>
chmod -R 0775 storage <br>

chown -R www-data:www-data bootstrap/ <br>
chmod -R 0775 bootstrap <br>




If you are testing api with Postman, then you should add  <br>
X-Requested-With:XMLHttpRequest <br>
To prevent validator sending you to web welcome page when sending invalid data

By default api will be opened at <url>http://localhost</url>



<br>
P.S Postman data located in postman file;
