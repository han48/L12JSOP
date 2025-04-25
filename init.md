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
php artisan vendor:publish --all
```

## Install laravel JetStream

```shell
composer require laravel/jetstream
php artisan jetstream:install inertia
```

## Install orchid platform
