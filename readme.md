[![Build Status](https://travis-ci.org/ereminIvan/laravelParser.svg)](https://travis-ci.org/ereminIvan/laravelParser)

$ mysql -uroot CREATE DATABASE `socialparser`CHARACTER SET utf8 COLLATE utf8_general_ci; CREATE USER 'socialparser'@'localhost' IDENTIFIED BY 'socialparser'; GRANT ALL PRIVILEGES ON *.* TO 'socialparser'@'localhost' WITH GRANT OPTION;

$ php composer.phar update

$ php artisan migrate

$ php artisan schedule:parse-sources
