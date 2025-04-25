# SETUP

## Create laravel latest

```shell
composer create-project laravel/laravel
cd laravel
```

## Install composer and npm

```shell
composer install && npm install && npm run build
```

## Publish vendor

```shell
php artisan stub:publish
php artisan vendor:publish --all
```

## Install laravel JetStream

```shell
composer require laravel/jetstream
php artisan jetstream:install inertia --teams
```

## Install orchid platform

```shell
composer require orchid/platform
php artisan orchid:install
php artisan orchid:admin
```

## Clear cache

```shell
php artisan clear-compiled
php artisan route:clear
php artisan event:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

## Clear database

```shell
php artisan migrate:fresh --seed
```

## Clear Test user

```shell
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE users;
TRUNCATE TABLE teams;
SET FOREIGN_KEY_CHECKS = 1;
```

## Check database record

```shell
SELECT * FROM migrations;
SELECT * FROM cache;
SELECT * FROM cache_locks;
SELECT * FROM failed_jobs;
SELECT * FROM job_batches;
SELECT * FROM jobs;
SELECT * FROM password_reset_tokens;
SELECT * FROM personal_access_tokens;
SELECT * FROM sessions;
SELECT * FROM team_invitations;
SELECT * FROM team_user;
SELECT * FROM teams;
SELECT * FROM users;
```

## Create storage link

```shell
php artisan storage:link
```

## Start serve

```shell
php artisan serve
```
