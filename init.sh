#!/bin/sh
composer install
touch database/database.sqlite
./artisan migrate