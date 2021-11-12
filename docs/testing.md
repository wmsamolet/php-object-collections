# Testing

### Get code from GitHub repository

```sh
git clone git@github.com:wmsamolet/php-object-collections.git
cd php-object-collections
```

### Install composer dependencies

```sh
composer install
```

or via docker-compose

```sh
docker-composer run php72 composer install
```

### Run check coding standards

```sh
composer phpcs
```

or via docker-compose

PHP 7.2
```sh
docker-composer run php72 composer phpcs
```

PHP 7.3
```sh
docker-composer run php73 composer phpcs
```

PHP 7.4
```sh
docker-composer run php74 composer phpcs
```

PHP 8.0
```sh
docker-composer run php80 composer phpcs
```

### Run unit tests

```sh
composer test
```

or via docker-compose

PHP 7.2
```sh
docker-composer run php72 composer test
```

PHP 7.3
```sh
docker-composer run php73 composer test
```

PHP 7.4
```sh
docker-composer run php74 composer test
```

PHP 8.0
```sh
docker-composer run php80 composer test
```