Dunkerque
=========

Docker hub & registry.

Written in PHP with Symfony.

**THIS PROJECT IS IN PRE-ALPHA STATE**

# INSTALL

Base

```sh
# Clone repository
git clone https://github.com/iamluc/dunkerque

# Enter directory
cd dunkerque
```

## On your machine

```sh
# Install dependencies with [composer](https://getcomposer.org/download/)
composer install

# Initialize database
php app/console doctrine:schema:create

# Create a user
php app/console fos:user:create

# Run test suite
./bin/behat
```

## With Docker (and docker-compose)

```sh
# Run server (You could have to change port)
docker-compose up -d

# Install dependencies
docker-compose run --rm app composer install

# Initialize database
docker-compose run --rm app app/console doctrine:schema:create

# Create a user
docker-compose run --rm app app/console fos:user:create

# Run test suite
docker-compose run --rm app bin/behat
```

# Storage

By default, your database and your layers will be stored in the cache folder (`app/cache`)

# LICENSE

[MIT](https://opensource.org/licenses/MIT)
