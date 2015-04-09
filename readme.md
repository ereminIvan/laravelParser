[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/downloads.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

$ mysql -uroot CREATE DATABASE `socialparser`CHARACTER SET utf8 COLLATE utf8_general_ci; CREATE USER 'socialparser'@'localhost' IDENTIFIED BY 'socialparser'; GRANT ALL PRIVILEGES ON *.* TO 'socialparser'@'localhost' WITH GRANT OPTION;

$ php composer.phar update

$ php artisan schedule:parse-sources
