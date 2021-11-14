# Contributing

**Attention! Project uses [PSR-12 coding standards](https://www.php-fig.org/psr/psr-12/)**

1. Fork the Project

1. Ensure you have Composer installed (see [Composer Download Instructions](https://getcomposer.org/download/))

1. Install Development Dependencies

    ``` sh
    composer install
    ```
   or via Docker
    ```
    docker-compose run php72 composer install
    ```

1. Create a Feature Branch

1. (Recommended) Run the Test Suite ([more details here](testing.md))

    ``` sh
    composer test
    ```
   or via Docker
    ```
    docker-compose run php72 composer test
    ```

1. (Recommended) Check whether your code conforms to our Coding Standards by running

    ``` sh
    composer phpcs
    ```
   or via Docker
    ```
    docker-compose run php72 composer phpcs
    ```

1. Send us a Pull Request